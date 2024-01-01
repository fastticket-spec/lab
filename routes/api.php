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
    Route::post('/login', [LoginController::class, 'checkinAppLogin']);

    Route::group(['middleware' => ['auth:api', 'checkin']], function () {
        Route::post('verify-attendee', [AttendeesController::class, 'checkAttendee']);
    });

    Route::group(['middleware' => ['auth:api', 'checkinout']], function () {
        Route::post('check-in', [AttendeesController::class, 'checkInAttendee']);
    });
});

Route::prefix('misc-user')->group(function () {
    Route::post('/login', [LoginController::class, 'scanAppLogin']);

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('scan-attendee', [AttendeesController::class, 'scanAttendee']);
    });
});


Route::post('send-data', [AttendeesController::class, 'createAttendeeViaApi']);
