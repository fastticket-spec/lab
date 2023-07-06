<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Services\AttendeeService;
use App\Services\BadgeService;
use App\Services\EventService;
use App\Services\OrganiserService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private OrganiserService $organiserService,
        private EventService     $eventService,
        private AttendeeService  $attendeeService,
        private BadgeService     $badgeService,
    )
    {
    }

    public function index(): \Inertia\Response
    {
        $user = auth()->user();
        $active_organiser = $user->account->active_organiser;

        $organiserData = [
            'name' => 'Organisers',
            'iI8' => 'sidebar.organisers',
            'icon' => 'ri-user-2-line',
            'count' => $this->organiserService->count(),
        ];

        $data = [
            [
                'name' => 'Categories',
                'iI8' => 'sidebar.events',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->eventService->count()
            ],
            [
                'name' => 'Attendees',
                'iI8' => 'sidebar.attendees',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->attendeeService->count()
            ],
            [
                'name' => 'Accepted',
                'iI8' => 'sidebar.accepted',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->attendeeService->count(approved: true)
            ],
            [
                'name' => 'Declined',
                'iI8' => 'sidebar.declined',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->attendeeService->count(declined: true)
            ],
            [
                'name' => 'Downloads',
                'iI8' => 'sidebar.downloads',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->attendeeService->countDownloads()
            ],
            [
                'name' => 'Badges',
                'iI8' => 'sidebar.badges',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->badgeService->count()
            ]
        ];

        if (!$active_organiser) {
            $data = [
                $organiserData,
                ...$data
            ];
        }

        return Inertia::render("Dashboard/Index", [
            'data' => $data,
            'attendees' => Attendee::with('event')->latest()->take(10)->get()
        ]);
    }
}
