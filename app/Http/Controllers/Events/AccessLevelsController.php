<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Services\AccessLevelsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccessLevelsController extends Controller
{
    public function __construct(private AccessLevelsService $accessLevelsService)
    {
    }

    public function index(Request $request, string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/AccessLevels/Index', [
            'access_levels' => $this->accessLevelsService->fetchAccessLevels($request, $eventId),
            'event_id' => $eventId
        ]);
    }

    public function create(string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/AccessLevels/Create', [
            'event_id' => $eventId
        ]);
    }

    public function store(Request $request, string $eventId)
    {
        $request->validate(['title' => 'required']);

        return $this->accessLevelsService->createAccessLevel($request->all(), $eventId);
    }

    public function edit(string $eventId, string $accessLevelId): \Inertia\Response
    {
        $accessLevel = $this->accessLevelsService->find($accessLevelId);

        return Inertia::render('Events/Event/AccessLevels/Create', [
            'event_id' => $eventId,
            'access_level' => $accessLevel,
            'editMode' => true
        ]);
    }

    public function update(Request $request, string $eventId, string $accessLevelId)
    {
        $request->validate(['title' => 'required']);

        return $this->accessLevelsService->updateAccessLevel($request, $eventId, $accessLevelId);
    }

    public function updateStatus(Request $request, string $eventId, string $accessLevelId)
    {
        return $this->accessLevelsService->updateAccessLevelStatus($request, $eventId, $accessLevelId);
    }
}
