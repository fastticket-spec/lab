<?php

use App\Http\Controllers\AccreditationController;
use App\Http\Controllers\AttendeesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Event\SurveyController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Events\AccessLevelsController;
use App\Http\Controllers\Events\DashboardController as EventDashboardController;
use App\Http\Controllers\EventSurveyController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrganiserController;
use App\Http\Controllers\ZonesController;
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

Route::get('/e/{event_id}/a/{access_level_id}', [AccreditationController::class, 'index']);
Route::get('/form/{access_level_id}', [AccreditationController::class, 'form']);
Route::post('/form/{event_id}/{access_level_id}/submit', [AccreditationController::class, 'formSubmit']);
Route::get('/form/{access_level_id}/success', [AccreditationController::class, 'formSuccess']);

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

    Route::group(['middleware' => 'active-organiser'], function () {
        Route::group(['prefix' => 'events'], function () {
            Route::get('/', [EventController::class, 'index']);
            Route::get('/create', [EventController::class, 'create']);
            Route::post('/', [EventController::class, 'store']);
            Route::post('/{id}/duplicate', [EventController::class, 'duplicateEvent']);
            Route::get('/{id}/edit', [EventController::class, 'edit']);
            Route::post('/{id}/update', [EventController::class, 'update']);
            Route::delete('/{id}', [EventController::class, 'destroy']);
            Route::post('/{id}/change-status', [EventController::class, 'changeStatus']);
            Route::get('/organiser-events', [EventController::class, 'fetchOrganiserEvents']);
        });

        Route::group(['prefix' => 'event/{id}'], function () {
            Route::get('/dashboard', [EventDashboardController::class, 'index']);

            Route::group(['prefix' => 'access-levels'], function () {
                Route::get('/', [AccessLevelsController::class, 'index']);
                Route::get('/create', [AccessLevelsController::class, 'create']);
                Route::post('/', [AccessLevelsController::class, 'store']);
                Route::get('{access_level_id}/edit', [AccessLevelsController::class, 'edit']);
                Route::patch('{access_level_id}/update', [AccessLevelsController::class, 'update']);
                Route::post('{access_level_id}/change-status', [AccessLevelsController::class, 'updateStatus']);
                Route::get('{access_level_id}/customize', [AccessLevelsController::class, 'customize']);
                Route::post('{access_level_id}/customize/general', [AccessLevelsController::class, 'customizeGeneral']);
                Route::post('{access_level_id}/customize/page-design', [AccessLevelsController::class, 'customizePageDesign']);
                Route::post('{access_level_id}/customize/design-images', [AccessLevelsController::class, 'designImages']);
                Route::post('{access_level_id}/customize/request-form', [AccessLevelsController::class, 'requestForm']);
                Route::post('{access_level_id}/customize/socials', [AccessLevelsController::class, 'socials']);
            });

            Route::prefix('/event-surveys')->group(function () {
                Route::get('/', [EventSurveyController::class, 'index']);
                Route::post('/{event_survey_id}/status', [EventSurveyController::class, 'status']);

                Route::get('/create', [SurveyController::class, 'create']);
                Route::post('/', [SurveyController::class, 'store']);
                Route::get('/{event_survey_id}/surveys', [SurveyController::class, 'index']);
                Route::patch('/{event_survey_id}/edit-surveys', [SurveyController::class, 'update']);
            });

            Route::prefix('attendees')->group(function () {
                Route::get('/', [AttendeesController::class, 'eventAttendees']);
                Route::post('/{attendee_id}/approval/{status}', [AttendeesController::class, 'approveEventAttendee']);
                Route::post('/{attendee_id}/send-message', [AttendeesController::class, 'sendEventAttendeeMessage']);
                Route::post('/{attendee_id}/assign-zones', [AttendeesController::class, 'assignEventZones']);
            });

            Route::prefix('/zones')->group(function () {
                Route::get('/', [ZonesController::class, 'index']);
                Route::get('/create', [ZonesController::class, 'create']);
                Route::post('/', [ZonesController::class, 'store']);
                Route::patch('/{zone_id}/update-status', [ZonesController::class, 'updateStatus']);
            });
        });

        Route::prefix('attendees')->group(function () {
            Route::get('/', [AttendeesController::class, 'index']);
            Route::post('/{attendee_id}/approval/{status}', [AttendeesController::class, 'approveAttendee']);
            Route::post('/{attendee_id}/send-message', [AttendeesController::class, 'sendAttendeeMessage']);
            Route::post('/{attendee_id}/assign-zones', [AttendeesController::class, 'assignZones']);
        });
    });
});
