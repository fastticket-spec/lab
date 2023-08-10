<?php

namespace App\Services;

use App\Http\Requests\AccessLevelGeneralRequest;
use App\Mail\InvitationMail;
use App\Models\AccessLevel;
use App\Models\EventSurvey;
use App\Models\Invite;
use App\Repositories\BaseRepository;
use App\Services\traits\HasFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AccessLevelsService extends BaseRepository
{
    use HasFile;

    protected $images_path;

    public function __construct(
        AccessLevel          $model,
        private FileService  $file,
        private EventService $eventService
    ) {
        parent::__construct($model);

        $this->images_path = config('filesystems.directory') . "access_level_images/";
    }

    public function fetchAccessLevels(Request $request, string $eventId)
    {
        return $this->model->query()
            ->with(['event', 'surveyAccessLevels.surveys', 'attendees'])
            ->whereEventId($eventId)
            ->latest()
            ->paginate($request->per_page ?: 10)
            ->withQueryString()
            ->through(function ($accessLevel) {
                $quantity = $accessLevel->quantity_available;

                return [
                    'id' => $accessLevel->id,
                    'title' => $accessLevel->title,
                    'title_arabic' => $accessLevel->title_arabic,
                    'quantity_available' => $quantity,
                    'quantity_filled' => $accessLevel->attendees->count(),
                    'event' => $accessLevel->event,
                    'status' => $accessLevel->status,
                    'public_status' => $accessLevel->public_status,
                    'registration' => $accessLevel->registration,
                    'attendees' => $accessLevel->attendees,
                    'has_surveys' => !!optional($accessLevel->surveyAccessLevels)->surveys
                ];
            });
    }

    // fetch all categories access leve
    public function fetchCategoryAccessLevels(Request $request, string $categoryID)
    {
        $getEvents = $this->eventService->fetchEventsId($categoryID);

        return $this->model->query()
            ->with(['event', 'surveyAccessLevels.surveys', 'attendees'])
            ->whereIn('event_id', $getEvents)
            ->whereStatus(1)
            ->wherePublicStatus(1)
            ->latest()
            ->paginate($request->per_page ?: 10)
            ->withQueryString()
            ->through(function ($accessLevel) {
                $quantity = $accessLevel->quantity_available;

                return [
                    'id' => $accessLevel->id,
                    'title' => $accessLevel->title,
                    'title_arabic' => $accessLevel->title_arabic,
                    'quantity_available' => $quantity,
                    'quantity_filled' => $accessLevel->attendees->count(),
                    'event' => $accessLevel->event,
                    'status' => $accessLevel->status,
                    'attendees' => $accessLevel->attendees,
                    'has_surveys' => !!optional($accessLevel->surveyAccessLevels)->surveys
                ];
            });
    }

    public function allAccessLevels(string $eventId, ?bool $excludeExisting = false, ?array $showIds = null): \Illuminate\Database\Eloquent\Collection|array
    {
        $excludeIds = [];
        if ($excludeExisting) {
            $event = $this->eventService->find($eventId);
            $badges = array_merge(...$event->badges()
                ->with('badgeAccessLevels')
                ->get()
                ->map(fn ($badge) => $badge->badgeAccessLevels)->toArray());

            $excludeIds = collect($badges)->pluck('access_level_id')->toArray();

            if ($showIds) {
                $excludeIds = array_diff($excludeIds, $showIds);
            }
        }

        return $this->model->query()
            ->whereEventId($eventId)
            ->whereNotIn('id', $excludeIds)
            ->latest()
            ->get();
    }

    public function createAccessLevel(array $data, string $eventId)
    {
        try {
            $accessLevel = $this->create($data + ['event_id' => $eventId]);

            $message = 'Access level created';
            return $this->view(data: ['access_level' => $accessLevel, 'message' => $message], flashMessage: $message, component: "/event/$eventId/access-levels", returnType: 'redirect');
        } catch (\Throwable $th) {
            \Log::error($th);
            $message = 'Could not create access level';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: "/event/$eventId/access-levels/create", returnType: 'redirect');
        }
    }

    public function updateAccessLevel(Request $request, string $eventId, string $accessLevelId)
    {
        try {
            $this->find($accessLevelId)->update($request->all());
            $message = 'Access level updated successfully.';

            return $this->view(data: ['message' => $message], flashMessage: $message, component: "/event/$eventId/access-levels", returnType: 'redirect');
        } catch (\Throwable $th) {
            \Log::error($th);
            $message = 'Could not update access level';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: "/event/$eventId/access-levels/$accessLevelId/edit", returnType: 'redirect');
        }
    }

    public function updateAccessLevelStatus(Request $request, string $eventId, string $accessLevelId)
    {
        try {
            $accessLevel = $this->find($accessLevelId);
            $accessLevel->update(['status' => $accessLevel->status ? 0 : 1]);

            $message = 'Access level status updated successfully.';

            return $this->view(data: ['message' => $message], flashMessage: $message, component: "/event/$eventId/access-levels", returnType: 'redirect');
        } catch (\Throwable $th) {
            \Log::error($th);
            $message = 'Could not update access level status';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: "/event/$eventId/access-levels/$accessLevelId/edit", returnType: 'redirect');
        }
    }

    public function updateAccessLevelPublicStatus(Request $request, string $eventId, string $accessLevelId)
    {
        try {
            $accessLevel = $this->find($accessLevelId);
            $accessLevel->update(['public_status' => $accessLevel->public_status ? 0 : 1]);

            $message = 'Access level status updated successfully.';

            return $this->view(data: ['message' => $message], flashMessage: $message, component: "/event/$eventId/access-levels", returnType: 'redirect');
        } catch (\Throwable $th) {
            \Log::error($th);
            $message = 'Could not update access level status';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: "/event/$eventId/access-levels/$accessLevelId/edit", returnType: 'redirect');
        }
    }




    public function updateGeneralCustomization(AccessLevelGeneralRequest $request, string $eventId, string $accessLevelId)
    {
        $route = "/event/$eventId/access-levels/$accessLevelId/customize?page=general";
        try {
            $accessLevel = $this->find($accessLevelId);
            $accessLevel->update([
                'title' => $request->title,
                'title_arabic' => $request->title_arabic,
                'quantity_available' => $request->quantity_available,
            ]);

            $accessLevel->generalSettings()->updateOrCreate(['access_level_id' => $accessLevelId], $request->all());

            $message = 'Access level settings updated';

            return $this->view(data: ['message' => $message], flashMessage: $message, component: $route, returnType: 'redirect');
        } catch (\Throwable $th) {
            \Log::error($th);

            $message = 'Could not update access level settings';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: $route, returnType: 'redirect');
        }
    }

    public function updatePageDesign(Request $request, string $eventId, string $accessLevelId)
    {
        $route = "/event/$eventId/access-levels/$accessLevelId/customize?page=design";
        try {
            $accessLevel = $this->find($accessLevelId);

            $accessLevel->pageDesign()->updateOrCreate(['access_level_id' => $accessLevelId], $request->all());

            $message = 'Access level page design updated';

            return $this->view(data: ['message' => $message], flashMessage: $message, component: $route, returnType: 'redirect');
        } catch (\Throwable $th) {
            \Log::error($th);

            $message = 'Could not update access level page design!';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: $route, returnType: 'redirect');
        }
    }

    public function uploadDesignImages(Request $request, string $eventId, string $accessLevelId)
    {
        try {
            $event = $this->eventService->find($eventId);

            foreach ($request->design_images as $img) {
                if (!is_uploaded_file($img)) {
                    continue;
                }

                $path = $this->uploadFile($img, 'event-', '-bg-', 1400);

                if (!$path) {
                    return false;
                }

                $event->organiser->designImages()->create(['event_id' => $event->id, 'design_image' => $path]);
            }

            $message = 'Event Images uploaded successfully';
            $route = "/event/$eventId/access-levels/$accessLevelId/customize?page=design";

            return $this->view(
                data: [
                    'message' => $message,
                    'data' => $event
                ],
                flashMessage: $message,
                component: $route,
                returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            $route = "/event/$eventId/access-levels/$accessLevelId/customize?page=design";

            \Log::error($th);

            $message = 'An error occurred while uploading image!';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: $route, returnType: 'redirect');
        }
    }

    public function uploadLogo(Request $request, string $eventId, string $accessLevelId)
    {
        try {
            $event = $this->eventService->find($eventId);

            $img = $request->file('logo');

            $path = $this->uploadFile($img, 'event-', '-logo-', 1400);

            if (!$path) {
                return false;
            }

            $accessLevel = $this->find($accessLevelId);
            $accessLevel->pageDesign()->updateOrCreate(['access_level_id' => $accessLevelId], ['logo' => Storage::disk(config('filesystems.default'))->url($path)]);

            $message = 'Event Images uploaded successfully';
            $route = "/event/$eventId/access-levels/$accessLevelId/customize?page=design&logouploaded=true";

            return $this->view(
                data: [
                    'message' => $message,
                    'data' => $event
                ],
                flashMessage: $message,
                component: $route,
                returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            $route = "/event/$eventId/access-levels/$accessLevelId/customize?page=design";

            \Log::error($th);

            $message = 'An error occurred while uploading logo!';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: $route, returnType: 'redirect');
        }
    }

    public function updateRequestForm(Request $request, string $eventId, string $accessLevelId)
    {
        $route = "/event/$eventId/access-levels/$accessLevelId/customize?page=request_form";
        try {
            $accessLevel = $this->find($accessLevelId);

            $accessLevel->requestForm()->updateOrCreate(['access_level_id' => $accessLevelId], $request->all());

            $message = 'Access level request form updated';

            return $this->view(data: ['message' => $message], flashMessage: $message, component: $route, returnType: 'redirect');
        } catch (\Throwable $th) {
            \Log::error($th);

            $message = 'Could not update access level request form!';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: $route, returnType: 'redirect');
        }
    }

    public function updateSocials(Request $request, string $eventId, string $accessLevelId)
    {
        $route = "/event/$eventId/access-levels/$accessLevelId/customize?page=socials";
        try {
            $accessLevel = $this->find($accessLevelId);

            $accessLevel->socials()->updateOrCreate(['access_level_id' => $accessLevelId], $request->all());

            $message = 'Access level socials updated';

            return $this->view(data: ['message' => $message], flashMessage: $message, component: $route, returnType: 'redirect');
        } catch (\Throwable $th) {
            \Log::error($th);

            $message = 'Could not update access level socials!';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: $route, returnType: 'redirect');
        }
    }

    public function sendLink(Request $request, string $eventId, string $accessLevelId)
    {
        $invitations = $request->invitations;
        $route = "/event/$eventId/access-levels";

        $accessLevel = $this->find($accessLevelId);

        $surveyLink = config('app.url') . '/a/' . $accessLevelId;
        $settings = $accessLevel->generalSettings;
        $ref = Str::random('8');

        $organiser = $accessLevel->event->organiser;

        $path = '';
        if ($request->hasFile('attachment')) {
            $path = $this->uploadFile($request->attachment, 'invitation-', '-attachment-');
//            $path = Storage::disk(config('filesystems.default'))->url($path);
        }

        foreach ($invitations as $invitation) {
            $email = $invitation['email'];
            $inviteId = Invite::create([
                'first_name' => $invitation['first_name'],
                'last_name' => $invitation['last_name'],
                'email' => $email,
                'ref' => $ref,
                'event_id' => $eventId,
                'access_level_id' => $accessLevelId,
                'attachment' => $path ? Storage::disk(config('filesystems.default'))->url($path) : null
            ])->id;

            Mail::to($email)
                ->later(now()->addSeconds(5), new InvitationMail(
                    settings: $settings,
                    surveyLink: "$surveyLink?ref=$inviteId",
                    organiser: $organiser,
                    firstName: $invitation['first_name'] ?: ($invitation['last_name'] ?: 'Applicant'),
                    registration: $accessLevel->registration,
                    ref: $ref,
                    attachment: $path
                ));
        }

        $message = 'Invitation has been sent to the emails supplied';

        return $this->view(data: ['message' => $message], flashMessage: $message, component: $route, returnType: 'redirect');
    }

    public function getSurveys(string $accessLevelId)
    {
        $accessLevel = $this->find($accessLevelId);

        $surveys = optional(optional($accessLevel->surveyAccessLevels)->eventSurvey)->surveys;

        return $this->view(['surveys' => $surveys]);
    }

    public function getInvites(string $accessLevelId)
    {
        $accessLevel = $this->find($accessLevelId);

        $invites = $accessLevel->invites()->latest()->get()->map(function ($invite) {
            return [
                'first_name' => $invite->first_name,
                'last_name' => $invite->last_name,
                'email' => $invite->email,
                'date_sent' => $invite->created_at->format('jS M, Y h:i a')
            ];
        });

        return $this->view(['invites' => $invites]);
    }
}
