<?php

namespace App\Http\Controllers;

use App\Services\ZoneService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ZonesController extends Controller
{
    public function __construct(private ZoneService $zoneService)
    {
    }

    public function index(Request $request, string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/Zones/Index', [
            'eventId' => $eventId,
            'zones' => $this->zoneService->fetchZones($request, $eventId)
        ]);
    }

    public function create(string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/Zones/Create', [
            'eventId' => $eventId,
            'zones' => $this->zoneService->findBy(['event_id' => $eventId])
        ]);
    }

    public function store(Request $request, string $eventId)
    {
        $request->validate(['zones' => 'required|array', 'zones.*.zone' => 'required|string']);

        return $this->zoneService->storeZones($request->zones, $eventId);
    }

    public function updateStatus(string $eventId, string $zoneId)
    {
        return $this->zoneService->updateStatus($eventId, $zoneId);
    }
}
