<?php

namespace App\Http\Controllers;

use App\Models\EventSurvey;
use App\Models\EventSurveyAccessLevel;
use App\Models\Survey;
use App\Services\AccessLevelsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccreditationController extends Controller
{
    public function __construct(private AccessLevelsService $accessLevelsService)
    {
    }

    public function index(string $eventId, string $accessLevelId): \Inertia\Response
    {
        $accessLevel = $this->accessLevelsService->find($accessLevelId);
        $accessLevel->load(['event', 'pageDesign', 'generalSettings']);

        return Inertia::render('Accreditation/Index', [
            'accessLevel' => $accessLevel
        ]);
    }

    public function form(Request $request, string $accessLevelId)
    {
        $accessLevel = $this->accessLevelsService->find($accessLevelId);
        $surveys = $accessLevel->surveyAccessLevels->eventSurvey->surveys;

        $accessLevel->load(['event', 'pageDesign', 'generalSettings']);

        return Inertia::render('Accreditation/Form', []);
    }
}
