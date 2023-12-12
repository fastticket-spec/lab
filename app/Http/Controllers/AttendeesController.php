<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendeeMessageRequest;
use App\Http\Requests\AttendeeUploadRequest;
use App\Models\Country;
use App\Services\AccessLevelsService;
use App\Services\AreaService;
use App\Services\AttendeeService;
use App\Services\EventService;
use App\Services\ZoneService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;
use Rap2hpoutre\FastExcel\FastExcel;
use Log;


class AttendeesController extends Controller
{
    public function __construct(public AttendeeService $attendeeService, private ZoneService $zoneService, private AccessLevelsService $accessLevelsService, private AreaService $areaService, private EventService $eventService)
    {
    }

    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('Attendees/Index', [
            'attendees' => $this->attendeeService->fetchAttendees($request),
            'zones' => $this->zoneService->allZones(),
            'areas' => $this->areaService->allAreas(),
            'sort' => $request->sort,
            'filter_by' => $request->filter,
            'q' => $request->q,
            'access_levels' => [],
            'categories' => []
        ]);
    }

    public function eventAttendees(Request $request, string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/Attendees/Index', [
            'eventId' => $eventId,
            'zones' => $this->zoneService->allZones($eventId),
            'areas' => $this->areaService->allAreas(),
            'attendees' => $this->attendeeService->fetchAttendees($request, $eventId),
            'sort' => $request->sort,
            'filter_by' => $request->filter,
            'q' => $request->q,
            'accessLevels' => $this->accessLevelsService->allAccessLevels($eventId),
            'categories' => $this->eventService->currentOrganiserEvents()
        ]);
    }

    public function approveAttendee(Request $request, string $attendeeId, int $status)
    {
        return $this->attendeeService->approveAttendee(attendeeId: $attendeeId, status: $status, page: $request->page);
    }

    public function approveEventAttendee(Request $request, string $eventId, string $attendeeId, int $status)
    {
        return $this->attendeeService->approveAttendee(attendeeId: $attendeeId, status: $status, eventId: $eventId, page: $request->page);
    }

    public function sendAttendeeMessage(AttendeeMessageRequest $request, string $attendeeId)
    {
        return $this->attendeeService->sendMessage($request->all(), $attendeeId);
    }

    public function sendEventAttendeeMessage(AttendeeMessageRequest $request, string $eventId, string $attendeeId)
    {
        return $this->attendeeService->sendMessage($request->all(), $attendeeId, $eventId);
    }

    public function assignZones(Request $request, string $attendeeId)
    {
        $request->validate(['zones' => 'required|array', 'zones.*' => 'required|string']);

        return $this->attendeeService->assignZones(zones: $request->zones, attendeeId: $attendeeId, page: $request->page);
    }

    public function assignEventZones(Request $request, string $eventId, string $attendeeId)
    {
        $request->validate(['zones' => 'required|array', 'zones.*' => 'required|string']);

        return $this->attendeeService->assignZones(zones: $request->zones, attendeeId: $attendeeId, eventId: $eventId, page: $request->page);
    }

    public function assignAreas(Request $request, string $attendeeId)
    {
        $request->validate(['areas' => 'required|array', 'areas.*' => 'required|string']);

        return $this->attendeeService->assignAreas(areas: $request->areas, attendeeId: $attendeeId, page: $request->page);
    }

    public function assignEventAreas(Request $request, string $eventId, string $attendeeId)
    {
        $request->validate(['areas' => 'required|array', 'areas.*' => 'required|string']);

        return $this->attendeeService->assignAreas(areas: $request->areas, attendeeId: $attendeeId, eventId: $eventId, page: $request->page);
    }

    public function bulkApproval(Request $request, int $status)
    {
        $request->validate(['attendee_ids' => 'array|required']);

        return $this->attendeeService->bulkApproveAttendee(attendeeIds: $request->attendee_ids, status: $status, page: $request->page);
    }

    public function bulkEventApproval(Request $request, string $eventId, int $status)
    {
        $request->validate(['attendee_ids' => 'array|required']);

        return $this->attendeeService->bulkApproveAttendee(attendeeIds: $request->attendee_ids, status: $status, eventId: $eventId, page: $request->page);
    }

    public function bulkAssignZones(Request $request)
    {
        $request->validate(['attendee_ids' => 'array|required', 'zones' => 'required|array', 'zones.*' => 'required|string']);

        return $this->attendeeService->bulkAssignZones(attendeeIds: $request->attendee_ids, zones: $request->zones, page: $request->page);
    }

    public function bulkAssignEventZones(Request $request, string $eventId)
    {
        $request->validate(['attendee_ids' => 'array|required', 'zones' => 'required|array', 'zones.*' => 'required|string']);

        return $this->attendeeService->bulkAssignZones(attendeeIds: $request->attendee_ids, zones: $request->zones, eventId: $eventId, page: $request->page);
    }

    public function bulkAssignAreas(Request $request)
    {
        $request->validate(['attendee_ids' => 'array|required', 'areas' => 'required|array', 'areas.*' => 'required|string']);

        return $this->attendeeService->bulkAssignAreas(attendeeIds: $request->attendee_ids, areas: $request->areas, page: $request->page);
    }

    public function bulkAssignEventAreas(Request $request, string $eventId)
    {
        $request->validate(['attendee_ids' => 'array|required', 'areas' => 'required|array', 'areas.*' => 'required|string']);

        return $this->attendeeService->bulkAssignAreas(attendeeIds: $request->attendee_ids, areas: $request->areas, eventId: $eventId, page: $request->page);
    }

    public function sendInvitation(string $attendeeId)
    {
        return $this->attendeeService->sendInvitation($attendeeId);
    }

    public function sendEventInvitation(string $eventId, string $attendeeId)
    {
        return $this->attendeeService->sendInvitation($attendeeId);
    }

    public function sendBulkInvitation(Request $request)
    {
        $request->validate(['attendee_ids' => 'array|required']);

        return $this->attendeeService->sendBulkInvitations($request->attendee_ids);
    }

    public function sendBulkEventInvitation(Request $request, string $eventId)
    {
        $request->validate(['attendee_ids' => 'array|required']);

        return $this->attendeeService->sendBulkInvitations($request->attendee_ids, $eventId);
    }

    public function updateAttendeeAnswers(Request $request, string $attendeeId)
    {
        return $this->attendeeService->updateAnswer($request, $attendeeId);
    }

    public function updateEventAttendeeAnswers(Request $request, string $eventId, string $attendeeId)
    {
        return $this->attendeeService->updateAnswer($request, $attendeeId, $eventId);
    }

    public function downloadBadge(Request $request, string $attendeeId, string $badgeId)
    {
        return $this->attendeeService->downloadAttendeeBadge($request->type, $attendeeId, $badgeId);
    }

    public function downloadEventBadge(Request $request, string $eventId, string $attendeeId, string $badgeId)
    {
        return $this->attendeeService->downloadAttendeeBadge($request->type, $attendeeId, $badgeId, $eventId);
    }

    public function uploadAttendees(AttendeeUploadRequest $request, string $eventId)
    {
        return $this->attendeeService->uploadAttendees($eventId, $request->attendees, $request->access_level_id, $request->approve, $request->mail, $request->whatsapp);
    }

    /**
     * @throws Throwable
     */
    public function checkAttendee(Request $request)
    {
        $request->validate(['attendee_ref' => 'required']);

        return $this->attendeeService->checkAttendee($request->attendee_ref);
    }

    public function checkInAttendee(Request $request)
    {
        $request->validate(['attendee_ref' => 'required']);

        return $this->attendeeService->checkinAttendee($request->attendee_ref);
    }

    public function scanAttendee(Request $request)
    {
        $request->validate(['attendee_ref' => 'required']);

        return $this->attendeeService->scanAttendee($request->attendee_ref);
    }

    /**
     * @throws Throwable
     */
    public function checkInAttendees(string $attendeeId, Request $request)
    {
        return $this->attendeeService->checkinAttendeeById($attendeeId, $request->page);
    }

    /**
     * @throws Throwable
     */
    public function checkInAttendeesEvent(string $eventId, string $attendeeId, Request $request)
    {
        return $this->attendeeService->checkinAttendeeById($attendeeId, $request->page, $eventId);
    }

    public function markAsPrinted(Request $request)
    {
        $request->validate(['attendee_ids' => 'required|array']);

        return $this->attendeeService->togglePrinted(
            attendee_ids: $request->attendee_ids,
            printed: $request->printed,
            page: $request->page
        );
    }

    public function markAsPrintedEvent(Request $request, string $eventId)
    {
        $request->validate(['attendee_ids' => 'required|array']);

        return $this->attendeeService->togglePrinted(
            attendee_ids: $request->attendee_ids,
            printed: $request->printed,
            eventId: $eventId,
            page: $request->page
        );
    }

    public function markAsCollected(Request $request)
    {
        $request->validate(['attendee_ids' => 'required|array']);

        return $this->attendeeService->toggleCollected(
            attendee_ids: $request->attendee_ids,
            collected: $request->collected,
            page: $request->page
        );
    }

    public function markAsCollectedEvent(Request $request, string $eventId)
    {
        $request->validate(['attendee_ids' => 'required|array']);

        return $this->attendeeService->toggleCollected(
            attendee_ids: $request->attendee_ids,
            collected: $request->collected,
            eventId: $eventId,
            page: $request->page
        );
    }

    public function incrementBadgeDownload(string $attendeeId)
    {
        return $this->attendeeService->incrementDownloads($attendeeId);
    }

    public function registerApplicant(string $eventId): \Inertia\Response
    {
        $surveys = [];
        if ($accessId = request()->accessId) {
            $accessLevel = $this->accessLevelsService->find($accessId);
            $surveys = $accessLevel->surveyAccessLevels->eventSurvey->surveys;
        }

        $countries = Country::all();

        return Inertia::render('Attendees/RegisterApplicants', [
            'accessLevels' => $this->accessLevelsService->allAccessLevels($eventId),
            'eventId' => $eventId,
            'accessId' => $accessId,
            'countries' => $countries,
            'surveys' => $surveys
        ]);
    }

    public function changeAccessLevel(Request $request, string $eventId, string $attendeeId)
    {
        $request->validate(['event_id' => 'required|exists:events,id', 'access_level_id' => 'required|exists:access_levels,id']);

        return $this->attendeeService->changeAccessLevel($eventId, $attendeeId, $request->access_level_id, $request->page, $request->event_id);
    }

    public function destroyAttendee(string $attendeeId)
    {
        return $this->attendeeService->deleteAttendee(attendeeId: $attendeeId, page: request()->page);
    }

    public function destroyEventAttendee(string $eventId, string $attendeeId)
    {
        return $this->attendeeService->deleteAttendee(attendeeId: $attendeeId, eventId: $eventId, page: request()->page);
    }

    public function pullSplDataPlayers(Request $request, $id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://splis.spl.com.sa/api/competition/preliminary/StageID/55/TeamID/' . $id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response, true);
        $response = collect($response);
        return (new FastExcel($response))->download($id . '-player.xlsx');
    }


    public function pullSplDataOfficials(Request $request, $id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://splis.spl.com.sa/api/competition/preliminaryofficial/StageID/55/TeamID/' . $id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response, true);
        $response = collect($response);
        return (new FastExcel($response))->download($id . '-official.xlsx');
    }

    public function export(string $eventId, string $accessLevelId): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $export = $this->attendeeService->export($eventId, $accessLevelId);

        return Excel::download($export, 'attendees.xlsx');
    }

    public function exportCheckins(string $eventId, string $accessLevelId): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $export = $this->attendeeService->exportCheckins($eventId, $accessLevelId);

        return Excel::download($export, 'checkins.xlsx');
    }

    public function PSPDFKit()
    {
        return $this->attendeeService->PSPDFKit();
    }

    public function createAttendeeViaApi(Request $request)
    {
        Log::info(json_encode($request->all()));
        $answers = null;

        if ($phone = $request->phone) {
            $answers = [
                ['type' => "1", "answer" => $phone, "question" => "Phone Number"]
            ];
        }

        $lang = 'english';

        $event = $this->accessLevelsService->find($request->access_level_id)->event;

        if ($event) {
            $attendee = $this->attendeeService->create([
                'access_level_id' => $request->access_level_id,
                'event_id' => $event->id,
                'ref' => Str::random('8'),
                'organiser_id' => $event->organiser_id,
                'first_name' => $first_name = $request->first_name,
                'last_name' => $request->last_name,
                'email' => $email = $request->email,
                'answers' => $answers
            ]);

            $settings = optional($attendee->accessLevel)->generalSettings;

            // Mail::to($email)->later(now()->addSeconds(5), new AttendeeMail($settings, $lang, $attendee->event->organiser, $first_name));

            return $this->view(['message' => 'Attendee created successfully']);
        } else {
            return $this->view(['message' => 'Event not found'], 404);
        }
    }
}
