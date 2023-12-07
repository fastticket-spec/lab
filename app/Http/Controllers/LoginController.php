<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function index(): \Inertia\Response
    {
        return Inertia::render('Auth/Login');
    }

    public function login(LoginRequest $request, ?string $userType = null)
    {
        $data = $this->authService->loginUser($request->only(['email', 'password']), $userType);

        if ($data['status'] != 200) {

            return $this->view(
                data: [
                    'message' => $data['message']
                ], statusCode: $data['status'], component: 'Auth/Login');
        }

        return $this->view(
            data: [
                'message' => 'Login successful',
                'access_token' => $data['accessToken'],
                'data' => $data['user']
            ], component: redirect()->intended()->getTargetUrl(), returnType: 'redirect'
        );
    }

    public function logout(): \Illuminate\Http\RedirectResponse
    {
        auth()->logout();
        return redirect('/login');
    }

    public function checkinAppLogin(LoginRequest $request)
    {
        return $this->login($request, 'checkin');
    }

    public function scanAppLogin(LoginRequest $request)
    {
        return $this->login($request, 'misc');
    }
}
