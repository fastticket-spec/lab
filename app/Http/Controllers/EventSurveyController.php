<?php

namespace App\Http\Controllers;

use App\Services\EventSurveyService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventSurveyController extends Controller
{
    public function __construct(private EventSurveyService $eventSurveyService)
    {
    }

    public function index(Request $request, string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/Surveys/EventSurveys', [
            'event_id' => $eventId,
            'event_surveys' => $this->eventSurveyService->fetchEventSurveys($request, $eventId)
        ]);
    }

    public function status(string $eventId, string $eventSurveyId)
    {
        return $this->eventSurveyService->updateStatus($eventId, $eventSurveyId);
    }
}
