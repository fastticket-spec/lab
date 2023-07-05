<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\EventBadge;
use App\Models\EventSurveyAccessLevel;
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
            'accessLevels' => $this->accessLevelsService->allAccessLevels($eventId, true)
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
            'accessLevels' => $this->accessLevelsService->allAccessLevels($eventId, true, $accessLevelIds->toArray())
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

    public function customize(string $eventId, string $badgeId)
    {
        $badge = $this->badgeService->find($badgeId);
        $accessLevelsId = $badge->badgeAccessLevels()->get()->map(fn($bAL) => $bAL->access_level_id);

        $questions = array_merge(...EventSurveyAccessLevel::with('surveys')
            ->whereIn('access_level_id', $accessLevelsId)
            ->get()
            ->map(fn($level) => $level->surveys)->toArray());

        $data = [
            'badge' => $this->badgeService->find($badgeId),
            'event' => $badge->event,
            'store_url' => "/event/$eventId/event-badges/$badgeId",
            'event_badge' => EventBadge::where('badge_id', $badgeId)->where('event_id', $eventId)->first(),
            'questions' => $questions
        ];
        return view('badge_customize', $data);
    }
}
