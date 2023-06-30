<?php

namespace App\Http\Controllers;

use App\Services\AccessLevelsService;
use App\Services\AttendeeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccreditationController extends Controller
{
    public function __construct(
        private AccessLevelsService $accessLevelsService,
        private AttendeeService $attendeeService,
    )
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

    public function form(Request $request, string $accessLevelId): \Inertia\Response
    {
        $accessLevel = $this->accessLevelsService->find($accessLevelId);
        $surveys = $accessLevel->surveyAccessLevels->eventSurvey->surveys;

        $accessLevel->load(['pageDesign', 'event', 'generalSettings']);

        return Inertia::render('Accreditation/Form', [
            'accessLevel' => $accessLevel,
            'surveys' => $surveys,
            'lang' => $request->lang
        ]);
    }

    public function formSubmit(Request $request, string $eventId, string $accessLevelId)
    {
        return $this->attendeeService->createAttendee($request, $eventId, $accessLevelId);
    }

    public function formSuccess(Request $request, string $accessLevelId): \Inertia\Response
    {
        $accessLevel = $this->accessLevelsService->find($accessLevelId);
        $accessLevel->load(['pageDesign', 'event', 'generalSettings']);

        return Inertia::render('Accreditation/FormSuccess', [
            'accessLevel' => $accessLevel,
            'lang' => $request->lang
        ]);
    }
}
