<?php

namespace App\Services;

use App\Models\Area;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AreaService extends BaseRepository
{
    public function __construct(Area $model, private EventService $eventService)
    {
        parent::__construct($model);
    }

    public function fetchAreas(Request $request, string $eventId): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->query()
            ->with(['event'])
            ->where('event_id', $eventId)
            ->latest()
            ->paginate($request->per_page ?: 10)
            ->withQueryString()
            ->through(function ($area) {
                return [
                    'id' => $area->id,
                    'area_id' => $area->id,
                    'area' => $area->area,
                    'event' => $area->event,
                    'status' => Area::STATUS_READABLE[$area->status],
                    'date_created' => $area->created_at->format('jS M, Y H:i')
                ];
            });
    }

    public function allAreas(?string $eventId = null): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->model->query()
            ->with(['event'])
            ->when($eventId, function ($query) use($eventId) {
                $query->whereEventId($eventId);
            })
            ->latest()
            ->get();
    }

    public function storeAreas(array $areasData, string $eventId)
    {
        try {
            $this->eventService->find($eventId)->areas()->delete();

            $areasData = collect($areasData)->map(fn($data) => [
                'id' => Str::uuid(),
                'area' => $data['area'],
                'event_id' => $eventId,
                'created_at' => now(),
                'updated_at' => now()
            ])->toArray();

            DB::table('areas')->insert($areasData);

            $message = 'Areas added successfully';

            return $this->view(
                data: ['message' => $message], flashMessage: $message, component: "/event/$eventId/areas", returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            \Log::error($th);

            $message = 'Areas could not be saved';

            return $this->view(
                data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: "/event/$eventId/areas/create", returnType: 'redirect'
            );
        }
    }

    public function updateStatus(string $eventId, string $areaId)
    {
        $area = $this->find($areaId);
        $area->update([
            'status' => $area->status == Area::STATUS['ACTIVE']
                ? Area::STATUS['INACTIVE']
                : Area::STATUS['ACTIVE']
        ]);

        $message = 'Area status updated successfully';

        return $this->view(
            data: ['message' => $message], flashMessage: $message, component: "/event/$eventId/areas", returnType: 'redirect'
        );
    }

    public function count(?string $eventId = null): int
    {
        $user = auth()->user();
        $account = $user->account;
        $activeOrganiser = $account->active_organiser;
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
