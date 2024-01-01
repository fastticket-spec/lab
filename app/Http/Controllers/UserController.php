<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\AddUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Event;
use App\Services\EventService;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(private UserService $userService, private RoleService $roleService, private EventService $eventService)
    {
    }

    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('Users/Index', [
            'users' => $this->userService->organiserUsers($request)
        ]);
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Users/Create', [
            'roles' => $this->roleService->fetchRoles(),
            'events' => Event::whereOrganiserId(auth()->user()->account->active_organiser)
                ->get(['id', 'title', 'title_arabic'])
        ]);
    }

    public function store(AddUserRequest $request)
    {
        return $this->userService->createUser($request);
    }

    public function edit(string $userId): \Inertia\Response
    {
        $user = $this->userService->find($userId);
        $user->load('account.eventAccess.event');

        $user->role_id = $user->account->role_id;
        $user->all_events = $user->account->access_all_events;
        $user->events_access = $user->account->eventAccess->map(fn ($eventAccess) => [
            'id' => optional($eventAccess->event)->id,
            'title' => optional($eventAccess->event)->title,
            'title_arabic' => optional($eventAccess->event)->title_arabic
        ]);

        return Inertia::render('Users/Create', [
            'user' => $user,
            'roles' => $this->roleService->all(['id', 'role']),
            'events' => Event::whereOrganiserId(auth()->user()->account->active_organiser)->get(['id', 'title', 'title_arabic']),
            'editMode' => true
        ]);
    }

    public function update(UpdateUserRequest $request, string $userId)
    {
        return $this->userService->updateUser($request, $userId);
    }

    public function destroy(string $userId)
    {
        return $this->userService->deleteUser($userId);
    }
}
