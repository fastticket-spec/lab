<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrganiserController;
use Illuminate\Support\Facades\Route;


Route::redirect('/', '/dashboard');

Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::get('/password-reset', [LoginController::class, 'passwordReset']);
Route::post('/send-password-reset-token', [LoginController::class, 'sendPasswordResetToken']);
Route::get('/verify-token', [LoginController::class, 'acceptToken']);
Route::post('/verify-token', [LoginController::class, 'verifyToken']);
Route::get('/change-password', [LoginController::class, 'changePassword']);
Route::post('/change-password', [LoginController::class, 'updatePassword']);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('home');

    Route::group(['prefix' => 'organisers'], function () {
        Route::get('/', [OrganiserController::class, 'index']);
        Route::get('/create', [OrganiserController::class, 'create']);
        Route::post('/create', [OrganiserController::class, 'store']);
        Route::get('/{id}/edit', [OrganiserController::class, 'edit']);
        Route::patch('/{id}', [OrganiserController::class, 'update']);
        Route::post('/{id}/edit-logo', [OrganiserController::class, 'updateLogos']);

        Route::post('/{id}/set-organiser', [OrganiserController::class, 'loginOrganiser']);
        Route::post('/{id}/unset-organiser', [OrganiserController::class, 'logoutOrganiser']);
    });
});
