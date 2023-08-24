<?php

namespace App\Services;

use App\Models\Zone;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ZoneService extends BaseRepository
{
    public function __construct(Zone $model, private EventService $eventService)
    {
        parent::__construct($model);
    }

    public function fetchZones(Request $request, string $eventId): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->query()
            ->with(['event'])
            ->where('event_id', $eventId)
            ->latest()
            ->paginate($request->per_page ?: 10)
            ->withQueryString()
            ->through(function ($zone) {
                return [
                    'id' => $zone->id,
                    'zone_id' => $zone->id,
                    'zone' => $zone->zone,
                    'event' => $zone->event,
                    'status' => Zone::STATUS_READABLE[$zone->status],
                    'date_created' => $zone->created_at->format('jS M, Y H:i')
                ];
            });
    }

    public function allZones(?string $eventId = null): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->model->query()
            ->with(['event'])
            ->when($eventId, function ($query) use($eventId) {
                $query->whereEventId($eventId);
            })
            ->latest()
            ->get();
    }

    public function storeZones(array $zonesData, string $eventId)
    {
        try {
            $this->eventService->find($eventId)->zones()->delete();

            $zonesData = collect($zonesData)->map(fn($data) => [
                'id' => Str::uuid(),
                'zone' => $data['zone'],
                'event_id' => $eventId,
                'created_at' => now(),
                'updated_at' => now()
            ])->toArray();

            DB::table('zones')->insert($zonesData);

            $message = 'Zones added successfully';

            return $this->view(
                data: ['message' => $message], flashMessage: $message, component: "/event/$eventId/zones", returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            \Log::error($th);

            $message = 'Zones could not be saved';

            return $this->view(
                data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: "/event/$eventId/zones/create", returnType: 'redirect'
            );
        }
    }

    public function updateStatus(string $eventId, string $zoneId)
    {
        $zone = $this->find($zoneId);
        $zone->update([
            'status' => $zone->status == Zone::STATUS['ACTIVE']
                ? Zone::STATUS['INACTIVE']
                : Zone::STATUS['ACTIVE']
        ]);

        $message = 'Zone status updated successfully';

        return $this->view(
            data: ['message' => $message], flashMessage: $message, component: "/event/$eventId/zones", returnType: 'redirect'
        );
    }

    public function count(?string $eventId = null): int
    {
        $user = auth()->user();
        $activeOrganiser = $user->activeOrganiser();
        if ($eventId) {
            $eventIds = [$eventId];
        } else {
            $eventIds = $this->eventService->model->query()
                ->when(!$activeOrganiser, function ($query) use ($user) {
                    $query->whereIn('organiser_id', $user->organiserIds());
                })
                ->when($activeOrganiser, function ($query) use ($activeOrganiser) {
                    $query->where('organiser_id', $activeOrganiser);
                })
                ->when($eventId, function ($query) use ($eventId) {
                    $query->where('event_id', $eventId);
                })
                ->pluck('id');
        }

        return $this->model->query()
            ->whereIn('event_id', $eventIds)
            ->count();
    }

}
