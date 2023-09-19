<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthService extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function loginUser(array $data): array
    {
        $userData = $this->findOneBy(['email' => $data['email']]);

        if (!$userData) {
            return [
                'message' => 'Email or password is incorrect!',
                'status' => 401
            ];
        }

        $remember = array_key_exists('remember', $data) ? $data['remember'] : false;

        if (!auth()->attempt($data, $remember)) {
            return [
                'message' => 'Email or password is incorrect!',
                'status' => 401
            ];
        }

        $user = $this->find(auth()->user()->id);

        $accessToken = $user->createToken('authenticate_user')->accessToken;

        return [
            'user' => $user,
            'accessToken' => $accessToken,
            'status' => 200
        ];
    }

    public function sendPasswordResetToken(string $email)
    {
        try {
            DB::beginTransaction();

            $token = Str::random();

            $user = $this->findOneByOrFail(['email' => $email]);

            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => now()
            ]);

            Http::messaging()->post("send-mail", [
                'to' => $email,
                'subject' => 'Password Reset | EasyTicket',
                'template' => 'password_reset',
                'template_type' => 'markdown',
                'params' => [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'resetToken' => $token
                ]
            ]);

            DB::commit();

            return $this->view(
                data: [], statusCode: 200, flashMessage: 'Password reset token has been sent to the supplied email', component: '/verify-token', returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            if ($th instanceof ModelNotFoundException) {
                return $this->view(
                    data: ['message' => 'User not found'],
                    statusCode: 404,
                    flashMessage: 'User not found',
                    messageType: 'danger',
                    component: '/reset-password',
                    returnType: 'redirect'
                );
            }
            \Log::error($th);
            return $this->view(
                data: ['message' => 'An error occurred while sending reset token'],
                statusCode: 400,
                flashMessage: 'An error occurred while sending reset token',
                messageType: 'danger',
                component: '/reset-password',
                returnType: 'redirect'
            );
        }
    }

    public function verifyToken(string $token)
    {
        $token = DB::table('password_resets')->whereToken($token)->first();

        $timeDiff = now()->diffInMinutes($token->created_at);

        if ($timeDiff > 10) {
            $message = 'Token has expired';
            return $this->view(
                data: ['message' => $message],
                statusCode: 400,
                flashMessage: $message,
                messageType: 'danger',
                component: "/verify-token",
                returnType: 'redirect'
            );
        }

        $message = 'Token valid';
        return $this->view(
            data: ['message' => $message],
            statusCode: 200,
            flashMessage: $message,
            component: "/change-password?token=$token->token",
            returnType: 'redirect'
        );
    }

    public function updatePassword(string $password, string $token)
    {
        $userToken = DB::table('password_resets')->whereToken($token)->first();

        if ($userToken) {
            $user = $this->findOneBy(['email' => $userToken->email]);
            $user->update(['password' => Hash::make($password)]);

            $message = 'Password updated.';
            return $this->view(
                data: ['message' => $message],
                statusCode: 200,
                flashMessage: $message,
                component: "/login",
                returnType: 'redirect'
            );
        }

        $message = 'Token invalid';
        return $this->view(
            data: ['message' => $message],
            statusCode: 400,
            flashMessage: $message,
            messageType: 'danger',
            component: "/change-password?token=$token",
            returnType: 'redirect'
        );
    }
}
