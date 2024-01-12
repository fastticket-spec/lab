<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
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
        $this->logActivity('logged out');

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

    public function passwordReset(): \Inertia\Response
    {
        return Inertia::render('Auth/PasswordReset');
    }

    public function sendPasswordResetToken(Request $request)
    {
        $request->validate(['email' => 'required|exists:users,email']);

        return $this->authService->sendPasswordResetToken($request->email);
    }

    public function acceptToken(): \Inertia\Response
    {
        return Inertia::render('Auth/AcceptToken');
    }

    public function verifyToken(Request $request)
    {
        $request->validate(['token' => 'required|exists:password_reset_tokens,token']);

        return $this->authService->verifyToken($request->token);
    }

    public function changePassword(): \Inertia\Response
    {
        return Inertia::render('Auth/ChangePassword', [
            'token' => request()->input('token')
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate(['password' => 'required', 'token' => 'required|exists:password_reset_tokens,token']);

        return $this->authService->updatePassword($request->password, $request->token);
    }
}
