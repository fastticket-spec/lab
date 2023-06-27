<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function signup(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required',
            'phone' => 'string|unique:users',
            'type' => 'string|in:oauth_user'
        ]);

        DB::beginTransaction();
        $user = $this->createUser($request->all());

        if (!$user) {
            return $this->view([
                'message' => 'could not create user'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->account()->create([
            'timezone_id' => config('settings.default_timezone'),
            'currency_id' => config('settings.default_currency')
        ]);

        DB::commit();

        return $this->view([
            'message' => 'User created successfully, login to continue'
        ], Response::HTTP_CREATED);
    }

    protected function createUser(array $data)
    {
        if (empty($data)) {
            return null;
        }

        $password = $data['password'];

        return User::create([
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($password)
        ]);
    }
}
