<?php

use App\Http\Controllers\AttendeesController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;


Route::prefix('user')->group(function () {
    Route::post('/signup', [RegistrationController::class, 'signup']);
    Route::post('/login', [LoginController::class, 'login']);
});

Route::prefix('checkin-user')->group(function () {
    Route::post('/login', [LoginController::class, 'scanAppLogin']);

    Route::group(['middleware' => ['auth:api', 'checkin']], function () {
        Route::post('verify-attendee', [AttendeesController::class, 'checkAttendee']);
        Route::post('check-in', [AttendeesController::class, 'checkInAttendee']);
    });
});


Route::post('send-data', [AttendeesController::class, 'createAttendeeViaApi']);
