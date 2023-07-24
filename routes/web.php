<?php

use App\Http\Controllers\AccreditationController;
use App\Http\Controllers\AttendeesController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Event\SurveyController;
use App\Http\Controllers\EventBadgeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Events\AccessLevelsController;
use App\Http\Controllers\Events\DashboardController as EventDashboardController;
use App\Http\Controllers\EventSurveyController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrganiserController;
use App\Http\Controllers\UserController;
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

Route::post('/event_badge_image_upload', [BadgeController::class, 'imageUpload']);

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

            Route::middleware('only-admin')->group(function () {
                Route::get('/create', [EventController::class, 'create']);
                Route::post('/', [EventController::class, 'store']);
                Route::post('/{id}/duplicate', [EventController::class, 'duplicateEvent']);
                Route::get('/{id}/edit', [EventController::class, 'edit']);
                Route::post('/{id}/update', [EventController::class, 'update']);
                Route::delete('/{id}', [EventController::class, 'destroy']);
                Route::post('/{id}/change-status', [EventController::class, 'changeStatus']);
                Route::get('/organiser-events', [EventController::class, 'fetchOrganiserEvents']);
            });
        });

        Route::group(['prefix' => 'event/{id}'], function () {
            Route::get('/dashboard', [EventDashboardController::class, 'index']);

            Route::group(['prefix' => 'access-levels'], function () {
                Route::get('/', [AccessLevelsController::class, 'index']);

                Route::middleware('can-edit')->group(function () {
                    Route::get('/create', [AccessLevelsController::class, 'create']);
                    Route::post('/', [AccessLevelsController::class, 'store']);
                    Route::group(['prefix' => '{access_level_id}'], function () {
                        Route::get('/edit', [AccessLevelsController::class, 'edit']);
                        Route::get('/surveys', [AccessLevelsController::class, 'getSurveys']);
                        Route::patch('/update', [AccessLevelsController::class, 'update']);
                        Route::post('/change-status', [AccessLevelsController::class, 'updateStatus']);
                        Route::get('/customize', [AccessLevelsController::class, 'customize']);
                        Route::post('/customize/general', [AccessLevelsController::class, 'customizeGeneral']);
                        Route::post('/customize/page-design', [AccessLevelsController::class, 'customizePageDesign']);
                        Route::post('/customize/design-images', [AccessLevelsController::class, 'designImages']);
                        Route::post('/customize/logo', [AccessLevelsController::class, 'logo']);
                        Route::post('/customize/request-form', [AccessLevelsController::class, 'requestForm']);
                        Route::post('/customize/socials', [AccessLevelsController::class, 'socials']);
                    });
                });

                Route::post('{access_level_id}/send-invitation', [AccessLevelsController::class, 'sendInvitationLink']);
            });

            Route::prefix('/event-surveys')->group(function () {
                Route::get('/', [EventSurveyController::class, 'index']);

                Route::middleware('can-edit')->group(function () {
                    Route::post('/{event_survey_id}/status', [EventSurveyController::class, 'status']);
                    Route::get('/create', [SurveyController::class, 'create']);
                    Route::post('/', [SurveyController::class, 'store']);
                    Route::get('/{event_survey_id}/surveys', [SurveyController::class, 'index']);
                    Route::patch('/{event_survey_id}/edit-surveys', [SurveyController::class, 'update']);
                });
            });

            Route::prefix('attendees')->group(function () {
                Route::get('/', [AttendeesController::class, 'eventAttendees']);
                Route::post('/bulk-approval/{status}', [AttendeesController::class, 'bulkEventApproval']);
                Route::post('/mark-as-printed', [AttendeesController::class, 'markAsPrintedEvent']);
                Route::post('/mark-as-collected', [AttendeesController::class, 'markAsCollectedEvent']);
                Route::post('/upload-attendees', [AttendeesController::class, 'uploadAttendees']);
                Route::post('/{attendee_id}/approval/{status}', [AttendeesController::class, 'approveEventAttendee']);
                Route::post('/{attendee_id}/send-message', [AttendeesController::class, 'sendEventAttendeeMessage']);
                Route::post('/bulk-assign-zones', [AttendeesController::class, 'bulkAssignEventZones']);
                Route::post('/send-bulk-invitation', [AttendeesController::class, 'sendBulkEventInvitation']);
                Route::post('/{attendee_id}/assign-zones', [AttendeesController::class, 'assignEventZones']);
                Route::post('/{attendee_id}/send-invitation', [AttendeesController::class, 'sendEventInvitation']);
                Route::post('/{attendee_id}/update-answers', [AttendeesController::class, 'updateEventAttendeeAnswers']);
                Route::get('/{attendee_id}/download-badge/{badge_id}', [AttendeesController::class, 'downloadEventBadge']);
            });

            Route::prefix('/zones')->group(function () {
                Route::get('/', [ZonesController::class, 'index']);
                Route::get('/create', [ZonesController::class, 'create']);
                Route::post('/', [ZonesController::class, 'store']);
                Route::patch('/{zone_id}/update-status', [ZonesController::class, 'updateStatus']);
            });

            Route::prefix('/badges')->group(function () {
                Route::get('/', [BadgeController::class, 'index']);

                Route::middleware('can-edit')->group(function () {
                    Route::get('/create', [BadgeController::class, 'create']);
                    Route::post('/', [BadgeController::class, 'store']);
                    Route::get('/{badge_id}/edit', [BadgeController::class, 'edit']);
                    Route::patch('/{badge_id}/update', [BadgeController::class, 'update']);
                    Route::get('/{badge_id}/customize', [BadgeController::class, 'customize']);
                });
            });

            Route::post('/event-badges/{badge_id}', [EventBadgeController::class, 'saveEventBadge']);
        });

        Route::prefix('attendees')->group(function () {
            Route::get('/', [AttendeesController::class, 'index']);
            Route::post('/bulk-approval/{status}', [AttendeesController::class, 'bulkApproval']);
            Route::post('/mark-as-printed', [AttendeesController::class, 'markAsPrinted']);
            Route::post('/mark-as-collected', [AttendeesController::class, 'markAsCollected']);
            Route::post('/{attendee_id}/approval/{status}', [AttendeesController::class, 'approveAttendee']);
            Route::post('/{attendee_id}/send-message', [AttendeesController::class, 'sendAttendeeMessage']);
            Route::post('/bulk-assign-zones', [AttendeesController::class, 'bulkAssignZones']);
            Route::post('/send-bulk-invitation', [AttendeesController::class, 'sendBulkInvitation']);
            Route::post('/{attendee_id}/assign-zones', [AttendeesController::class, 'assignZones']);
            Route::post('/{attendee_id}/send-invitation', [AttendeesController::class, 'sendInvitation']);
            Route::post('/{attendee_id}/update-answers', [AttendeesController::class, 'updateAttendeeAnswers']);
            Route::get('/{attendee_id}/download-badge/{badge_id}', [AttendeesController::class, 'downloadBadge']);
            Route::post('/{attendee_id}/download-badge-increment', [AttendeesController::class, 'incrementBadgeDownload']);
        });
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/create', [UserController::class, 'create']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user_id}/edit', [UserController::class, 'edit']);
        Route::patch('/{user_id}', [UserController::class, 'update']);
        Route::delete('/{user_id}', [UserController::class, 'destroy']);
    });
});

Route::prefix('checkin-user')->group(function () {
    Route::group(['middleware' => ['checkin']], function () {
        Route::post('verify-attendee', [AttendeesController::class, 'checkAttendee']);
    });
});
