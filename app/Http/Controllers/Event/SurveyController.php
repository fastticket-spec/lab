<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Services\AccessLevelsService;
use App\Services\EventService;
use App\Services\EventSurveyService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SurveyController extends Controller
{
    public function __construct(
        private EventService $eventService,
        private AccessLevelsService $accessLevelsService,
        private EventSurveyService $eventSurveyService
    )
    {
    }

    public function index(Request $request, string $eventId): \Inertia\Response
    {
        $eventSurvey = $this->eventSurveyService->findOneBy(['event_id' => $eventId]);

        if ($eventSurvey) {
            $eventSurvey->load('surveys');
        }

        return Inertia::render('Events/Event/Surveys/Index', [
            'event_id' => $eventId,
            'field_types' => config('formfields.field_types'),
            'access_levels' => $this->accessLevelsService->findBy(['status' => 1, 'event_id' => $eventId]),
            'event_survey' => $eventSurvey
        ]);
    }

    public function store(Request $request, string $eventId)
    {
        return $this->eventSurveyService->createSurvey($request, $eventId);
    }
}
