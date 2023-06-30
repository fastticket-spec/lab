<?php

namespace App\Services;

use App\Models\Event;
use App\Repositories\BaseRepository;
use App\Services\traits\HasFile;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class EventService extends BaseRepository
{
    use HasFile;

    protected string $images_path;

    public function __construct(Event $model, private OrganiserService $organiserService, public FileService $file)
    {
        parent::__construct($model);
        $this->images_path = config('filesystems.directory') . "event_images/";
    }

    public function fetchEvents(Request $request): LengthAwarePaginator
    {
        $account = auth()->user()->account;

        return $this->model->query()
            ->with('organiser')
            ->when($account->active_organiser, function ($query) use ($account) {
                $query->where('organiser_id', $account->active_organiser);
            })
            ->when($request->input('organiser_id'), function (Builder $query) use ($request) {
                $query->where('organiser_id', $request->organiser_id);
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
                    'day' => $event->created_at->format('d')
                ];
            });
    }

    public function createEvent(array $data, UploadedFile $eventImage = null, UploadedFile $eventBanner = null)
    {
        $organiser = $this->organiserService->find(auth()->user()->account->active_organiser);

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
}
