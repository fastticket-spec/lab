<?php

namespace App\Services;

use App\Models\Event;
use App\Repositories\BaseRepository;
use App\Services\traits\HasFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EventService extends BaseRepository
{
    use HasFile;

    protected string $images_path;

    public function __construct(Event $model, private OrganiserService $organiserService, public FileService $file, private AccountEventAccessService $accountEventAccessService)
    {
        parent::__construct($model);
        $this->images_path = config('filesystems.directory') . "event_images/";
    }

    public function fetchEventsId(string $organiserId): array
    {
        return $this->model->query()
            ->when($organiserId, function (Builder $query) use ($organiserId) {
                $query->where('organiser_id', $organiserId);
            })
            ->pluck('id')
            ->toArray();
    }

    public function fetchEvents(Request $request): LengthAwarePaginator
    {
        $user = auth()->user();
        $account = $user->parentAccount ?: $user->account;
        $roleId = $account->role_id;
        $activeOrganiser = $user->activeOrganiser();

        $eventsAccessID = null;
        if ($roleId) {
            $eventsAccessID = $this->accountEventAccessService->findBy(['account_id' => $account->id])->map(fn ($access) => $access->event_id);
        }

        return $this->model->query()
            ->with(['organiser', 'attendees'])
            ->when($activeOrganiser, function ($query) use ($activeOrganiser) {
                $query->where('organiser_id', $activeOrganiser);
            })
            ->when($request->input('organiser_id'), function (Builder $query) use ($request) {
                $query->where('organiser_id', $request->organiser_id);
            })
            ->when($eventsAccessID, function ($query) use ($eventsAccessID) {
                $query->whereIn('id', $eventsAccessID);
            })
            ->when($request->input('q'), function ($query) use ($request) {
                $searchTerm = $request->q;
                $query->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('title_arabic', 'like', "%{$searchTerm}%")
                    ->orWhereHas('organiser', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    });
            })
            ->when($request->input('sort'), function ($query) use ($request) {
                switch ($request->sort) {
                    case 'sort_by_creation':
                        $query->orderByDesc('created_at');
                        break;
                    case 'sort_by_title':
                        $query->orderBy('title');
                        break;
                    default:
                        $query->orderByDesc('created_at');
                }
            })
            ->latest()
            ->paginate($request->per_page ?: 10)
            ->withQueryString()
            ->through(function ($event) {
                $attendeesQuery = $event->attendees();
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'title_arabic' => $event->title_arabic,
                    'description' => $event->description,
                    'description_arabic' => $event->description_arabic,
                    'organiser_name' => optional($event->organiser)->name,
                    'event_image_url' => $event->event_image_url,
                    'event_banner_url' => $event->event_banner_url,
                    'status' => Event::EVENT_STATUS_READABLE[$event->status],
                    'month' => $event->created_at->format('M'),
                    'day' => $event->created_at->format('d'),
                    'no_of_attendees' => $attendeesQuery->count(),
                    'downloads' => $attendeesQuery->sum('downloads')
                ];
            });
    }

    public function createEvent(array $data, UploadedFile $eventImage = null, UploadedFile $eventBanner = null)
    {
        $organiser = $this->organiserService->find(auth()->user()->activeOrganiser());

        if ($eventImage) {
            $eventImage = $this->uploadFile($eventImage, $data['title'], '-event-image-');
        }

        if ($eventBanner) {
            $eventBanner = $this->uploadFile($eventBanner, $data['title'], '-event-banner-');
        }

        $data['logo'] = $eventImage;
        $data['banner'] = $eventBanner;

        $event = $organiser->events()->create($data);

        if (!$event) {
            $this->removeUploadedFile($eventImage);

            return false;
        }

        return $event;
    }

    public function processDuplicateEvent(string $eventId): Event
    {
        $event = $this->findOneOrFail($eventId)->toArray();
        $event['title'] = "[COPY] {$event['title']}";
        return $this->create($event);
    }

    public function editEvent(Event $event, array $data, UploadedFile $eventImage = null, UploadedFile $eventBanner = null): bool
    {
        DB::beginTransaction();
        $oldEventImage = null;
        $oldEventBanner = null;

        if ($eventImage) {
            $eventImage = $this->uploadFile($eventImage, $data['title'] ?? $event->title, '-event-image-');
            $oldEventImage = $event->logo;
        }

        if ($eventBanner) {
            $eventBanner = $this->uploadFile($eventBanner, $data['title'] ?? $event->title, '-event-banner-');
            $oldEventBanner = $event->banner;
        }

        $updatedEvent = $this->update($data + [
            'logo' => $eventImage ?: $event->logo,
            'banner' => $eventBanner ?: $event->banner,
        ], $event->id);

        if (!$updatedEvent) {
            $this->removeUploadedFile($eventImage);

            return false;
        }

        $this->removeUploadedFile($oldEventImage);

        DB::commit();

        return true;
    }

    public function processChangeStatus(string $eventId): string
    {
        $event = $this->findOneOrFail($eventId);
        $this->update(['status' => !$event['status']], $eventId);
        $event->refresh();
        return $event['status'] ? "activated" : "deactivated";
    }

    public function count(bool $all = false, array|Collection|null $allowedEventIds = null): int
    {
        if ($all) return $this->model->query()->count();

        $user = auth()->user();
        $account = $user->parentAccount ?: $user->account;
        $activeOrganiser = $user->account->active_organiser;

        return $this->model->query()
            ->when(!$activeOrganiser, function ($query) use ($user) {
                $query->whereIn('organiser_id', $user->organiserIds());
            })
            ->when($activeOrganiser, function ($query) use ($activeOrganiser) {
                $query->where('organiser_id', $activeOrganiser);
            })
            ->when($allowedEventIds, function ($query) use ($allowedEventIds) {
                $query->whereIn('id', $allowedEventIds);
            })
            ->count();
    }

    public function currentOrganiserEvents()
    {
        $organiserId = auth()->user()->activeOrganiser();

        return $this->model->query()
            ->whereOrganiserId($organiserId)
            ->whereStatus(Event::EVENT_STATUS['ACTIVE'])
            ->get();
    }
}
