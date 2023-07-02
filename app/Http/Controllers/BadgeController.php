<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Services\AccessLevelsService;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BadgeController extends Controller
{
    public function __construct(public BadgeService $badgeService, public AccessLevelsService $accessLevelsService)
    {
    }

    public function index(Request $request, string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/Badges/Index', [
            'eventId' => $eventId,
            'badges' => $this->badgeService->fetchBadges($request, $eventId)
        ]);
    }

    public function create(string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/Badges/Create', [
            'eventId' => $eventId,
            'accessLevels' => $this->accessLevelsService->allAccessLevels($eventId)
        ]);
    }

    public function store(Request $request, string $eventId)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'access_levels' => 'array|nullable',
            'access_levels.*' => 'string|required',
            'width' => 'required',
            'height' => 'required'
        ]);

        return $this->badgeService->createBadge($request, $eventId);
    }

    public function edit(string $eventId, string $badgeId): \Inertia\Response
    {
        $badge = $this->badgeService->find($badgeId);
        $accessLevelIds = $badge->badgeAccessLevels->map(fn($accessLevel) => $accessLevel->access_level_id);

        return Inertia::render('Events/Event/Badges/Create', [
            'eventId' => $eventId,
            'badge' => $badge,
            'editMode' => true,
            'accessLevelIds' => $accessLevelIds,
            'accessLevels' => $this->accessLevelsService->allAccessLevels($eventId)
        ]);
    }

    public function update(Request $request, string $eventId, string $badgeId)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'access_levels' => 'array|nullable',
            'access_levels.*' => 'string|required',
            'width' => 'required',
            'height' => 'required'
        ]);

        return $this->badgeService->updateBadge($request, $eventId, $badgeId);
    }
}
