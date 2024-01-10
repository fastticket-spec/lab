<?php

namespace App\Services;

use App\Http\Requests\User\AddUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Mail\NewManager;
use App\Mail\NewOrganiserUser;
use App\Models\Role;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService extends BaseRepository
{
    public function __construct(User $model, private OrganiserService $organiserService)
    {
        parent::__construct($model);
    }

    public function organiserUsers(Request $request): LengthAwarePaginator
    {
        $activeOrganiser = auth()->user()->activeOrganiser();

        return $this->model->query()
            ->with('account.eventAccess.event', 'account.role')
            ->whereHas('account', function ($q) {
                $q->whereNotNull('role_id');
            })
            ->when($activeOrganiser, function ($query) use ($activeOrganiser) {
                $query->whereHas('account', function ($q) use ($activeOrganiser) {
                    $q->whereActiveOrganiser($activeOrganiser);
                });
            })
            ->when($request->input('sort'), function ($query) use ($request) {
                switch ($request->sort) {
                    case 'sort_by_creation':
                        $query->orderByDesc('created_at');
                        break;
                    case 'sort_by_title':
                        $query->orderBy('title');
                        break;
                    case 'sort_by_start_date':
                        $query->orderBy('start_date');
                        break;
                    default:
                        $query->orderByDesc('created_at');
                }
            })
            ->latest()
            ->paginate($request->perPage ?: 10)
            ->withQueryString()
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'role' => optional($user->account->role)->role,
                'access_all_events' => $user->account->access_all_events,
                'event_access' => $user->account->eventAccess->map(fn ($x) => optional($x->event)->title)
            ]);
    }

    public function createUser(AddUserRequest $request): mixed
    {
        try {
            DB::beginTransaction();

            $user = $this->create($request->all() + [
                'password' => Hash::make($password = 'OPeration123@')
            ]);

            $account = auth()->user()->account;
            $account = $user->account()->create([
                'active_organiser' => $account->active_organiser,
                'timezone_id' => config('settings.default_timezone'),
                'currency_id' => config('settings.default_currency'),
                'role_id' => $request->role_id,
                'access_all_events' => !!$request->all_events
            ]);

            $eventAccesses = collect($request->event_ids)->map(fn ($x) => [
                'id' => Str::uuid(),
                'event_id' => $x,
                'account_id' => $account->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $account->eventAccess()->insert($eventAccesses->toArray());

            $organiser = $this->organiserService->find($account->active_organiser);

            Mail::to($user->email)->later(now()->addSeconds(3), new NewOrganiserUser("$user->first_name $user->last_name", $account->role->role, $user->email, $password, $organiser));

            $role = Role::find($request->role_id);
            $this->logActivity("added $user->fullName as $role->role", $user, $user->toArray());

            DB::commit();
            $message = 'User created successfully!';

            return $this->view(
                data: [
                    'user' => $user,
                    'message' => $message
                ],
                flashMessage: $message,
                component: '/users',
                returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            $message = 'An error occurred while creating the user!';

            return $this->view(
                data: [
                    'message' => $message,
                    'error' => $th->getMessage()
                ],
                flashMessage: $message,
                messageType: 'danger',
                component: '/users/create',
                returnType: 'redirect'
            );
        }
    }

    public function updateUser(UpdateUserRequest $request, string $userId): mixed
    {
        try {
            DB::beginTransaction();
            $user = $this->find($userId);

            $user->update($request->only(['first_name', 'last_name', 'email', 'phone']));

            $account = $user->account;

            $account->update([
                'role_id' => $request->role_id,
                'access_all_events' => $request->all_events
            ]);

            $account->eventAccess()->delete();

            $eventAccesses = collect($request->event_ids)->map(fn ($x) => [
                'id' => Str::uuid(),
                'event_id' => $x,
                'account_id' => $account->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $account->eventAccess()->insert($eventAccesses->toArray());

            $this->logActivity("updated $user->fullName user account", $user, $user->toArray());
            DB::commit();

            $message = 'User updated successfully!';

            return $this->view(
                data: [
                    'user' => $user,
                    'message' => $message
                ],
                flashMessage: $message,
                component: '/users',
                returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            $message = 'An error occurred while updating the user!';

            return $this->view(
                data: [
                    'message' => $message,
                    'error' => $th->getMessage()
                ],
                flashMessage: $message,
                messageType: 'danger',
                component: "/users/$userId/edit",
                returnType: 'redirect'
            );
        }
    }

    public function deleteUser(string $userId): mixed
    {
        DB::beginTransaction();

        $user = $this->find($userId);
        $user->account->eventAccess()->delete();
        $user->account->delete();

        $this->logActivity("deleted $user->fullName user account", $user, $user->toArray());
        $user->delete();

        DB::commit();

        $message = 'User deleted successfully!';

        return $this->view(
            data: [
                'message' => $message
            ],
            flashMessage: $message,
            component: '/users',
            returnType: 'redirect'
        );
    }

    public function fetchAccountManagers(Request $request): LengthAwarePaginator
    {
        $user = auth()->user();

        if ($parent = $user->parentAccount) {
            $parentUserId = $parent->owner;
            $parentAccountId = $user->parent_account_id;

            $query = $this->model->query()
                ->where('parent_account_id', $parentAccountId)
                ->orWhere('id', $parentUserId);
        } else {
            $query = $this->model->query()
                ->where('parent_account_id', $user->account->id)
                ->orWhere('id', $user->id);
        }

        return $query
            ->latest()
            ->paginate($request->perPage ?: 10)
            ->withQueryString()
            ->through(fn ($u) => [
                'id' => $u->id,
                'first_name' => $u->first_name,
                'last_name' => $u->last_name,
                'email' => $u->email,
                'parent_account_id' => $u->parent_account_id,
                'is_current_user' => $user->id == $u->id
            ]);
    }

    public function createAccountManager(Request $request)
    {
        try {
            $user = auth()->user();
            $account = $user->parentAccount ?: $user->account;
            $password = Str::password(length: 10, symbols: false);
            $manager = $this->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'parent_account_id' => $account->id
            ]);

            $manager->account()->create([
                'active_organiser' => null,
                'timezone_id' => config('settings.default_timezone'),
                'currency_id' => config('settings.default_currency'),
            ]);

            Mail::to($manager->email)->later(now()->addSeconds(3), new NewManager("$manager->first_name $manager->last_name", $manager->email, $password));

            $message = 'Account manager created successfully';

            $this->logActivity("created an account manager", $manager, ['first_name' => $manager->first_name, 'last_name' => $manager->last_name, 'email' => $manager->email]);

            return $this->view(
                data: [
                    'message' => $message
                ],
                flashMessage: $message,
                component: '/account-managers',
                returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            \Log::error($th);

            return $this->view(
                data: [
                    'message' => 'An error occurred'
                ],
                flashMessage: 'An error occurred',
                component: '/account-managers',
                returnType: 'redirect'
            );
        }
    }

    public function deleteAccountManager(string $accountManagerId)
    {
        $accountManager = $this->find($accountManagerId);
        if ($accountManager->parent_account_id) {
            $this->logActivity("deleted account manager ($accountManager->email)", $accountManager, ['first_name' => $accountManager->first_name, 'last_name' => $accountManager->last_name, 'email' => $accountManager->email]);
            $this->delete($accountManagerId);
        }

        $message = 'Account manager deleted';
        return $this->view(
            data: [
                'message' => $message
            ],
            flashMessage: $message,
            component: '/account-managers',
            returnType: 'redirect'
        );
    }
}
