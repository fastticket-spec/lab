<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountManagerController extends Controller
{
    public function __construct(public UserService $userService)
    {
    }

    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('AccountManagers/Index', [
            'managers' => $this->userService->fetchAccountManagers($request)
        ]);
    }

    public function create(Request $request): \Inertia\Response
    {
        return Inertia::render('AccountManagers/Create');
    }

    public function store(Request $request)
    {
        $request->validate(['first_name' => 'required', 'last_name' => 'required', 'email' => 'unique:users,email']);

        return $this->userService->createAccountManager($request);
    }

    public function destroy(string $accountManagerId)
    {
        return $this->userService->deleteAccountManager($accountManagerId);
    }
}
