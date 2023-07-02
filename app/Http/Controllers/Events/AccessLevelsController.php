<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccessLevelGeneralRequest;
use App\Services\AccessLevelsService;
use App\Services\EventService;
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

    public function __construct(private AccessLevelsService $accessLevelsService, private EventService $eventService)
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
        $event = $this->eventService->find($eventId);

        $page = request()->page ?? 'general';

        $designImages = [];
        if ($page == 'general') {
            $data = $accessLevel->generalSettings;
        } else if ($page == 'design') {
            $accessLevel->load('generalSettings');
            $data = $accessLevel->pageDesign;
            $designImages = $event->organiser->designImages;
        } else if ($page == 'request_form') {
            $data = $accessLevel->requestForm;
        } else {
            $data = $accessLevel->socials;
        }

        return Inertia::render('Events/Event/AccessLevels/Customize', [
            'event' => $event,
            'access_level' => $accessLevel,
            'menuLists' => $this->menuLists,
            'currentMenu' => $page,
            'data' => $data,
            'design_images' => $designImages
        ]);
    }

    public function customizeGeneral(AccessLevelGeneralRequest $request, string $eventId, string $accessLevelId)
    {
        return $this->accessLevelsService->updateGeneralCustomization($request, $eventId, $accessLevelId);
    }

    public function customizePageDesign(Request $request, string $eventId, string $accessLevelId)
    {
        return $this->accessLevelsService->updatePageDesign($request, $eventId, $accessLevelId);
    }

    public function designImages(Request $request, string $eventId, string $accessLevelId)
    {
        $request->validate([
            'design_images' => 'required|array',
            'design_images.*' => 'mimes:jpeg,jpg,png|max:4000'
        ]);

        return $this->accessLevelsService->uploadDesignImages($request, $eventId, $accessLevelId);
    }

    public function requestForm(Request $request, string $eventId, string $accessLevelId)
    {
        return $this->accessLevelsService->updateRequestForm($request, $eventId, $accessLevelId);
    }

    public function socials(Request $request, string $eventId, string $accessLevelId)
    {
        return $this->accessLevelsService->updateSocials($request, $eventId, $accessLevelId);
    }
}
