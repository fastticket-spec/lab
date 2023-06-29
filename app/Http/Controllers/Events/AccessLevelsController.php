<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccessLevelGeneralRequest;
use App\Services\AccessLevelsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccessLevelsController extends Controller
{
    private array $menuLists = [
        ['id' => 'general', 'name' => 'General', 'iI8' => 'customize.general'],
        ['id' => 'design', 'name' => 'Event Page Design', 'iI8' => 'customize.design'],
        ['id' => 'request_form', 'name' => 'Request Form', 'iI8' => 'customize.request_form'],
        ['id' => 'socials', 'name' => 'Socials', 'iI8' => 'customize.socials']
    ];

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

    public function customize(string $eventId, string $accessLevelId): \Inertia\Response
    {
        $accessLevel = $this->accessLevelsService->find($accessLevelId);

        $page = request()->page ?? 'general';

        $data = [];
        if ($page == 'general') {
            $data = $accessLevel->generalSettings;
        }

        return Inertia::render('Events/Event/AccessLevels/Customize', [
            'eventId' => $eventId,
            'access_level' => $accessLevel,
            'menuLists' => $this->menuLists,
            'currentMenu' => $page,
            'data' => $data
        ]);
    }

    public function customizeGeneral(AccessLevelGeneralRequest $request, string $eventId, string $accessLevelId)
    {
        return $this->accessLevelsService->updateGeneralCustomization($request, $eventId, $accessLevelId);
    }
}
