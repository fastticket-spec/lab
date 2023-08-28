<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Services\AccountEventAccessService;
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
        private AccountEventAccessService $accountEventAccessService
    )
    {
    }

    public function index(): \Inertia\Response
    {
        $user = auth()->user();
        $userRole = $user->userRole();
        $account = $user->parentAccount ?: $user->account;
        $active_organiser = $user->account->active_organiser;

        $roleId = $account->role_id;

        $eventsAccessID = null;
        if ($roleId) {
            $eventsAccessID = $this->accountEventAccessService->findBy(['account_id' => $account->id])->map(fn($access) => $access->event_id);
        }

        if ($userRole === 'Checkin Users') {
            return Inertia::render('Dashboard/CheckInUserDashboard');
        }

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
                'count' => $this->eventService->count(allowedEventIds: $eventsAccessID)
            ],
            [
                'name' => 'Attendees',
                'iI8' => 'sidebar.attendees',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->attendeeService->count(allowedEventIds: $eventsAccessID)
            ],
            [
                'name' => 'Accepted',
                'iI8' => 'sidebar.accepted',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->attendeeService->count(approved: true, allowedEventIds: $eventsAccessID),
                'disabled_for' => ['Operations']
            ],
            [
                'name' => 'Declined',
                'iI8' => 'sidebar.declined',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->attendeeService->count(declined: true, allowedEventIds: $eventsAccessID),
                'disabled_for' => ['Operations']
            ],
            [
                'name' => 'Downloads',
                'iI8' => 'sidebar.downloads',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->attendeeService->countDownloads(allowedEventIds: $eventsAccessID),
                'disabled_for' => ['Operations']
            ],
            [
                'name' => 'Badges',
                'iI8' => 'sidebar.badges',
                'icon' => 'ri-calendar-2-line',
                'count' => $this->badgeService->count(allowedEventIds: $eventsAccessID),
                'disabled_for' => []
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
            'attendees' => []
//        'attendees' => Attendee::with('event')
//                ->latest()
//                ->when($active_organiser, function ($query) use ($active_organiser) {
//                    $query->whereOrganiserId($active_organiser);
//                })
//                ->when($eventsAccessID, function ($query) use ($eventsAccessID) {
//                    $query->whereIn('event_id', $eventsAccessID);
//                })
//                ->take(10)
//                ->get()
        ]);
    }
}
