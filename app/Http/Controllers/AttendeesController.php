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
            'sort' => $request->sort
        ]);
    }

    public function eventAttendees(Request $request, string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/Attendees/Index', [
            'eventId' => $eventId,
            'zones' => $this->zoneService->allZones($eventId),
            'attendees' => $this->attendeeService->fetchAttendees($request, $eventId),
            'sort' => $request->sort
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
}
