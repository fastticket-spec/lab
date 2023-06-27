<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
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

});
