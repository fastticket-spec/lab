<?php

namespace App\Services;

use App\Models\Badge;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BadgeService extends BaseRepository
{
    public function __construct(Badge $model, private EventService $eventService)
    {
        parent::__construct($model);
    }

    public function fetchBadges(Request $request, string $eventId): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->query()
            ->with(['event', 'badgeAccessLevels.accessLevel'])
            ->whereEventId($eventId)
            ->latest()
            ->paginate($request->per_page ?: 10)
            ->withQueryString()
            ->through(function ($badge) {
                $accessLevels = $badge->badgeAccessLevels->map(fn($accessLevel) => optional($accessLevel->accessLevel)->title);

                return [
                    'id' => $badge->id,
                    'title' => $badge->title,
                    'event_id' => $badge->event_id,
                    'event' => $badge->event,
                    'description' => $badge->description,
                    'width' => $badge->width,
                    'height' => $badge->height,
                    'status' => $badge->status,
                    'date_created' => $badge->created_at->format('jS M, Y H:i'),
                    'access_levels' => $accessLevels
                ];
            });
    }

    public function createBadge(Request $request, string $eventId)
    {
        $badge = $this->create($request->all() + [
                'event_id' => $eventId
            ]);

        if ($accessLevels = $request->access_levels) {
            foreach ($accessLevels as $accessLevel) {
                $badge->badgeAccessLevels()->create([
                    'access_level_id' => $accessLevel
                ]);
            }
        }

        $message = 'Badge Created';

        return $this->view(
            data: ['data' => $badge, 'message' => $message],
            flashMessage: $message,
            component: "/event/$eventId/badges",
            returnType: 'redirect'
        );
    }

    public function updateBadge(Request $request, string $eventId, string $badgeId)
    {
        $badge = $this->find($badgeId);
        $badge->update($request->all());

        $badge->badgeAccessLevels()->delete();

        if ($accessLevels = $request->access_levels) {
            foreach ($accessLevels as $accessLevel) {
                $badge->badgeAccessLevels()->create([
                    'access_level_id' => $accessLevel
                ]);
            }
        }

        $message = 'Badge Updated';

        return $this->view(
            data: ['data' => [], 'message' => $message],
            flashMessage: $message,
            component: "/event/$eventId/badges",
            returnType: 'redirect'
        );
    }

    public function count(?string $eventId = null, array|Collection|null $allowedEventIds = null): int
    {
        $user = auth()->user();
        $account = $user->account;
        $activeOrganiser = $account->active_organiser;

        if ($eventId) {
            $eventIds = [$eventId];
        } else if ($allowedEventIds) {
            $eventIds = $allowedEventIds;
        } else {
            $eventIds = $this->eventService->model->query()
                ->when(!$activeOrganiser, function ($query) use ($user) {
                    $query->whereIn('organiser_id', $user->organiserIds());
                })
                ->when($activeOrganiser, function ($query) use ($activeOrganiser) {
                    $query->where('organiser_id', $activeOrganiser);
                })
                ->when($eventId, function ($query) use ($eventId) {
                    $query->where('id', $eventId);
                })
                ->pluck('id');
        }

        return $this->model->query()
            ->whereIn('event_id', $eventIds)
            ->count();
    }
}
