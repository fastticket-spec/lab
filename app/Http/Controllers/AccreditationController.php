<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CountryCode;
use App\Models\FormEmail;
use App\Models\Invite;
use App\Services\AccessLevelsService;
use App\Services\AttendeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AccreditationController extends Controller
{
    public function __construct(
        private AccessLevelsService $accessLevelsService,
        private AttendeeService $attendeeService,
    ) {
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
        $countryCodes = CountryCode::all();

        $accessLevel->load(['pageDesign', 'event.organiser', 'generalSettings', 'attendees']);

        if ($quantityAvailable = $accessLevel->quantity_available) {
            $quantityFilled = $accessLevel->attendees->count();
            if ($quantityFilled >= $quantityAvailable) {
                $status = false;
            }
        }

        $email = null;
        $reference = '';

        $attendee = null;
        if (strlen($request->ref) == 36) {
            $invite = Invite::find($request->ref);
            if ($invite) {
                $reference = $invite->ref;
                $email = $invite->email;
                $attendee = $this->attendeeService->findOneBy(['ref' => $reference]);
            } else {
                $reference = $request->ref;
            }
        }

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
            'countryCodes' => $countryCodes,
            'email' => $email,
            'attendee' => $attendee
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

        $generalSettings = $accessLevel->generalSettings;

        $success_message = optional($accessLevel->generalSettings)->success_message;
        $success_message_arabic = optional($accessLevel->generalSettings)->success_message_arabic;

        $link = optional($generalSettings)->link_address;

        return Inertia::render('Accreditation/FormSuccess', [
            'accessLevel' => $accessLevel,
            'lang' => $request->lang,
            'success_message' => $success_message,
            'success_message_arabic' => $success_message_arabic,
            'social_share' => !!optional($generalSettings)->share_link,
            'socials' => optional($generalSettings)->selected_socials ? json_decode($generalSettings->selected_socials) : [],
            'social_share_message' => optional($generalSettings)->social_share_message,
            'social_share_message_arabic' => optional($generalSettings)->social_share_message_arabic,
            'link' => $link
        ]);
    }

    public function login(Request $request, string $accessLevelId): \Illuminate\Http\JsonResponse
    {
        $inviteQuery = Invite::whereAccessLevelId($accessLevelId)->whereEmail($request->email)->whereRef($request->registration_number);

        return response()->json(['status' => $inviteQuery->exists()]);
    }

    public function saveFormEmails(Request $request): void
    {
        $request->validate([
            'email' => 'required|email',
            'event_id' => 'required|exists:events,id',
            'access_level_id' => 'required|exists:access_levels,id'
        ]);

        $formEmailQuery = FormEmail::query()->where(['email' => $request->email, 'access_level_id' => $request->access_level_id]);

        if ($formEmailQuery->exists()) {
            $formEmailQuery->update([
                'event_id' => $request->event_id,
                'organiser_id' => $request->organiser_id,
                'severity' => $formEmailQuery->first()->severity + 1
            ]);
        } else {
            FormEmail::query()->create([
                'email' => $request->email,
                'access_level_id' => $request->access_level_id,
                'event_id' => $request->event_id,
                'organiser_id' => $request->organiser_id,
            ]);
        }
    }
}
