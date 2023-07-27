<?php

namespace App\Http\Controllers;

use App\Services\AreaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AreasController extends Controller
{
    public function __construct(private AreaService $areaService)
    {
    }

    public function index(Request $request, string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/Areas/Index', [
            'eventId' => $eventId,
            'areas' => $this->areaService->fetchAreas($request, $eventId)
        ]);
    }

    public function create(string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/Areas/Create', [
            'eventId' => $eventId,
            'areas' => $this->areaService->findBy(['event_id' => $eventId])
        ]);
    }

    public function store(Request $request, string $eventId)
    {
        $request->validate(['areas' => 'required|array', 'areas.*.area' => 'required|string']);

        return $this->areaService->storeAreas($request->areas, $eventId);
    }

    public function updateStatus(string $eventId, string $areaId)
    {
        return $this->areaService->updateStatus($eventId, $areaId);
    }
}
