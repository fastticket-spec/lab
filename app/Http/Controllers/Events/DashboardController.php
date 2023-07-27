<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Models\AccessLevel;
use App\Models\Attendee;
use App\Services\AccessLevelsService;
use App\Services\AccountEventAccessService;
use App\Services\AttendeeService;
use App\Services\BadgeService;
use App\Services\EventService;
use App\Services\OrganiserService;
use App\Services\ZoneService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private AttendeeService $attendeeService,
        private BadgeService    $badgeService,
        private ZoneService     $zoneService,
        private EventService     $eventService,
        private AccountEventAccessService $accountEventAccessService,
        private AccessLevelsService $accessLevelsService
    ) {
    }

    public function index(string $eventId): \Inertia\Response
    {
        $data = [
            [
                'name' => 'Attendees',
                'iI8' => 'sidebar.attendees',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->attendeeService->count(eventId: $eventId)
            ],
            [
                'name' => 'Accepted',
                'iI8' => 'sidebar.accepted',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->attendeeService->count(approved: true, eventId: $eventId)
            ],
            [
                'name' => 'Declined',
                'iI8' => 'sidebar.declined',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->attendeeService->count(declined: true, eventId: $eventId)
            ],
            [
                'name' => 'Downloads',
                'iI8' => 'sidebar.downloads',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->attendeeService->countDownloads($eventId)
            ],
            [
                'name' => 'Badges',
                'iI8' => 'sidebar.badges',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->badgeService->count($eventId)
            ],
            [
                'name' => 'Zones',
                'iI8' => 'sidebar.zones',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->zoneService->count($eventId)
            ]
        ];

        return Inertia::render('Events/Event/Dashboard', [
            'data' => $data,
            'attendees' => Attendee::with('event')->whereEventId($eventId)->latest()->take(10)->get()
        ]);
    }


    public function public(Request $request, string $categryId): \Inertia\Response
    {
        $data = $this->accessLevelsService->fetchCategoryAccessLevels($request, $categryId);

        return Inertia::render('Events/Event/Public', [
            'data' => $data,
        ]);
    }
}
