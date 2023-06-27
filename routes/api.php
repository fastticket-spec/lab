<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;


Route::prefix('user')->group(function () {
    Route::post('/signup', [RegistrationController::class, 'signup']);
    Route::post('/login', [LoginController::class, 'login']);
});
