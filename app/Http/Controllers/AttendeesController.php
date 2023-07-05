<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendeeMessageRequest;
use App\Services\AttendeeService;
use App\Services\ZoneService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendeesController extends Controller
{
    public function __construct(public AttendeeService $attendeeService, private ZoneService $zoneService)
    {
    }

    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('Attendees/Index', [
            'attendees' => $this->attendeeService->fetchAttendees($request),
            'zones' => $this->zoneService->allZones(),
            'sort' => $request->sort,
            'q' => $request->q
        ]);
    }

    public function eventAttendees(Request $request, string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/Attendees/Index', [
            'eventId' => $eventId,
            'zones' => $this->zoneService->allZones($eventId),
            'attendees' => $this->attendeeService->fetchAttendees($request, $eventId),
            'sort' => $request->sort,
            'q' => $request->q
        ]);
    }

    public function approveAttendee(string $attendeeId, int $status)
    {
        return $this->attendeeService->approveAttendee($attendeeId, $status);
    }

    public function approveEventAttendee(string $eventId, string $attendeeId, int $status)
    {
        return $this->attendeeService->approveAttendee($attendeeId, $status, $eventId);
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

        return $this->attendeeService->assignZones($request->zones, $attendeeId);
    }

    public function assignEventZones(Request $request, string $eventId, string $attendeeId)
    {
        $request->validate(['zones' => 'required|array', 'zones.*' => 'required|string']);

        return $this->attendeeService->assignZones($request->zones, $attendeeId, $eventId);
    }

    public function bulkApproval(Request $request, int $status)
    {
        $request->validate(['attendee_ids' => 'array|required']);

        return $this->attendeeService->bulkApproveAttendee($request->attendee_ids, $status);
    }

    public function bulkEventApproval(Request $request, string $eventId, int $status)
    {
        $request->validate(['attendee_ids' => 'array|required']);

        return $this->attendeeService->bulkApproveAttendee($request->attendee_ids, $status, $eventId);
    }

    public function bulkAssignZones(Request $request)
    {
        $request->validate(['attendee_ids' => 'array|required', 'zones' => 'required|array', 'zones.*' => 'required|string']);

        return $this->attendeeService->bulkAssignZones($request->attendee_ids, $request->zones);
    }

    public function bulkAssignEventZones(Request $request, string $eventId)
    {
        $request->validate(['attendee_ids' => 'array|required', 'zones' => 'required|array', 'zones.*' => 'required|string']);

        return $this->attendeeService->bulkAssignZones($request->attendee_ids, $request->zones, $eventId);
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
        return $this->attendeeService->downloadAttendeeBadge($request, $attendeeId, $badgeId);
    }

    public function downloadEventBadge(Request $request, string $eventId, string $attendeeId, string $badgeId)
    {
        return $this->attendeeService->downloadAttendeeBadge($request, $attendeeId, $badgeId, $eventId);
    }
}
