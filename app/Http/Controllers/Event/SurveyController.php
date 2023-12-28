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

    public function index(string $eventId, string $eventSurveyId): \Inertia\Response
    {
        $accessLevels = $this->eventService
            ->find($eventId)
            ->accessLevels()
            ->whereStatus(1)
            ->whereDoesntHave('surveyAccessLevels', function ($query) use ($eventSurveyId) {
                $query->where('event_survey_id', '!=', $eventSurveyId);
            })
            ->get();

        $eventSurvey = $this->eventSurveyService->find($eventSurveyId);

        if ($eventSurvey) {
            $eventSurvey->load(['surveys', 'surveyAccessLevels']);
        }

        return Inertia::render('Events/Event/Surveys/Index', [
            'event_id' => $eventId,
            'field_types' => config('formfields.field_types'),
            'access_levels' => $accessLevels->toArray(),
            'event_survey' => $eventSurvey,
            'survey_access_levels' => $eventSurvey ? $eventSurvey->surveyAccessLevels->pluck('access_level_id') : []
        ]);
    }

    public function create(string $eventId): \Inertia\Response
    {
        $accessLevels = $this->eventService
            ->find($eventId)
            ->accessLevels()
            ->whereStatus(1)
            ->whereDoesntHave('surveyAccessLevels')
            ->get();

        return Inertia::render('Events/Event/Surveys/Index', [
            'event_id' => $eventId,
            'field_types' => config('formfields.field_types'),
            'access_levels' => $accessLevels,
            'event_survey' => null
        ]);
    }

    public function store(Request $request, string $eventId)
    {
        return $this->eventSurveyService->createSurvey($request, $eventId);
    }

    public function update(Request $request, string $eventId, string $eventSurveyId)
    {
        return $this->eventSurveyService->updateSurvey($request, $eventId, $eventSurveyId);
    }

    public function uploadTandCFile(Request $request)
    {
        $request->validate([
            'file' => 'file'
        ]);

        return $this->view(['file' => $this->eventSurveyService->uploadTandCFile($request)]);
    }
}
