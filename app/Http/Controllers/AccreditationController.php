<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Invite;
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
        return $this->indexNew($accessLevelId);
    }

    public function indexNew(string $accessLevelId): \Inertia\Response
    {
        $accessLevel = $this->accessLevelsService->find($accessLevelId);

        $status = !!$accessLevel->status;
        $accessLevel->load(['event.organiser', 'pageDesign', 'generalSettings', 'attendees']);

        if ($quantityAvailable = $accessLevel->quantity_available) {
            $quantityFilled = $accessLevel->attendees->count();
            if ($quantityFilled >= $quantityAvailable) {
                $status = false;
            }
        }

        return Inertia::render('Accreditation/Index', [
            'accessLevel' => $accessLevel,
            'status' => $status,
            'reference' => request()->ref
        ]);
    }

    public function form(Request $request, string $accessLevelId): \Inertia\Response
    {
        $accessLevel = $this->accessLevelsService->find($accessLevelId);
        $status = !!$accessLevel->status;
        $surveys = $accessLevel->surveyAccessLevels->eventSurvey->surveys;
        $countries = Country::all();

        $accessLevel->load(['pageDesign', 'event.organiser', 'generalSettings', 'attendees']);

        if ($quantityAvailable = $accessLevel->quantity_available) {
            $quantityFilled = $accessLevel->attendees->count();
            if ($quantityFilled >= $quantityAvailable) {
                $status = false;
            }
        }

        $email = '';
        $reference = '';

        if (strlen($request->ref) == 36) {
            $invite = Invite::find($request->ref);
            if ($invite) {
                $reference = $invite->ref;
                $email = $invite->email;
            } else {
                $reference = $request->ref;
            }
        }

        $reference = strlen($request->ref) == 36 ? optional(Invite::find($request->ref))->ref : $request->ref ;
        $answers = null;
        if ($reference) {
            $answers = optional($this->attendeeService->findOneBy(['ref' => $reference]))->answers;
        }

        return Inertia::render('Accreditation/Form', [
            'accessLevel' => $accessLevel,
            'status' => !!$status,
            'surveys' => $surveys,
            'lang' => $request->lang,
            'reference' => $reference,
            'answers' => $answers,
            'countries' => $countries,
            'email' => $email
        ]);
    }

    public function formSubmit(Request $request, string $eventId, string $accessLevelId)
    {
        return $this->attendeeService->createAttendee($request, $eventId, $accessLevelId);
    }

    public function formSuccess(Request $request, string $accessLevelId): \Inertia\Response
    {
        $accessLevel = $this->accessLevelsService->find($accessLevelId);
        $accessLevel->load(['pageDesign', 'event.organiser', 'generalSettings']);

        return Inertia::render('Accreditation/FormSuccess', [
            'accessLevel' => $accessLevel,
            'lang' => $request->lang
        ]);
    }

    public function login(Request $request, string $accessLevelId): \Illuminate\Http\JsonResponse
    {
        $inviteQuery = Invite::whereAccessLevelId($accessLevelId)->whereEmail($request->email)->whereRef($request->registration_number);

        return response()->json(['status' => $inviteQuery->exists()]);
    }
}
