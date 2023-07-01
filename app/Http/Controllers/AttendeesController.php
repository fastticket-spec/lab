<?php

namespace App\Http\Controllers;

use App\Services\AttendeeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendeesController extends Controller
{
    public function __construct(public AttendeeService $attendeeService)
    {
    }

    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('Attendees/Index', [
            'attendees' => $this->attendeeService->fetchAttendees($request),
            'sort' => $request->sort
        ]);
    }

    public function eventAttendees(Request $request, string $eventId): \Inertia\Response
    {
        return Inertia::render('Events/Event/Attendees/Index', [
            'eventId' => $eventId,
            'attendees' => $this->attendeeService->fetchAttendees($request, $eventId),
            'sort' => $request->sort
        ]);
    }

    public function approveAttendee(string $attendeeId)
    {
        return $this->attendeeService->approveAttendee($attendeeId);
    }

    public function approveEventAttendee(string $eventId, string $attendeeId)
    {
        return $this->attendeeService->approveAttendee($attendeeId, $eventId);
    }
}
