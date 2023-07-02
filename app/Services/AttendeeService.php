<?php

namespace App\Services;

use App\Mail\ApprovalMail;
use App\Mail\AttendeeMail;
use App\Mail\CustomAttendeeMail;
use App\Mail\InvitationMail;
use App\Models\Attendee;
use App\Models\AttendeeZone;
use App\Repositories\BaseRepository;
use App\Services\traits\HasFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendeeService extends BaseRepository
{
    use HasFile;

    protected string $images_path;

    public function __construct(Attendee $model, public FileService $file, public EventService $eventService, public AccessLevelsService $accessLevelsService)
    {
        parent::__construct($model);

        $this->images_path = config('filesystems.directory') . "accreditation_images/";
    }

    public function fetchAttendees(Request $request, ?string $eventId = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $account = auth()->user()->account;

        return $this->model->query()
            ->with(['event', 'accessLevel', 'zones.zone'])
            ->when($account->active_organiser, function ($query) use ($account) {
                $query->where('organiser_id', $account->active_organiser);
            })
            ->when($eventId, function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            })
            ->when($request->input('q'), function ($query) use ($request) {
                $searchTerm = $request->q;
                $query->where('email', 'like', "%{$searchTerm}%")
                    ->orWhere('ref', 'like', "%{$searchTerm}%")
                    ->orWhereHas('event', function ($q) use ($searchTerm) {
                        $q->where('title', 'like', "%{$searchTerm}%")
                            ->orWhere('title_arabic', 'like', "%{$searchTerm}%");
                    });
            })
            ->when($request->input('sort'), function ($query) use ($request) {
                switch ($request->sort) {
                    case 'sort_by_creation':
                        $query->orderByDesc('created_at');
                        break;
                    case 'sort_by_ref':
                        $query->orderBy('ref');
                        break;
                    case 'accept_status':
                        $query->orderBy('accept_status');
                        break;
                    default:
                        $query->orderByDesc('created_at');
                }
            })
            ->latest()
            ->paginate($request->per_page ?: 10)
            ->withQueryString()
            ->through(function ($attendee) {
                return [
                    'id' => $attendee->id,
                    'access_level' => $attendee->accessLevel,
                    'event' => $attendee->event,
                    'ref' => $attendee->ref,
                    'answers' => $attendee->answers,
                    'email' => $attendee->email,
                    'status' => Attendee::STATUS_READABLE[$attendee->status],
                    'accept_status' => Attendee::ACCEPT_STATUS_READABLE[$attendee->accept_status],
                    'date_submitted' => $attendee->created_at->format('jS M, Y H:i'),
                    'zones' => $attendee->zones->map(fn($zone) => $zone->zone_id)
                ];
            });
    }

    public function createAttendee(Request $request, string $eventId, string $accessLevelId)
    {
        $lang = $request->lang;
        $event = $this->eventService->find($eventId);
        $settings = $this->accessLevelsService->find($accessLevelId)->generalSettings;

        try {
            DB::beginTransaction();

            $email = '';
            $answers = [];
            foreach ($request->answers as $answer) {
                if ($answer['type'] == '5') {
                    $email = $answer['answer'];
                }
                if ($answer['type'] == '4' && ($file = $answer['answer'])) {
                    $fileUrl = $this->uploadFile($file, $answer['question'], '-accreditation-file-');
                    $answers[] = ['type' => $answer['type'], 'question' => $answer['question'], 'answer' => Storage::disk(config('filesystems.default'))->url($fileUrl)];
                } else {
                    $answers[] = ['type' => $answer['type'], 'question' => $answer['question'], 'answer' => $answer['answer'] ?? ''];
                }
            }

            $attendee = $this->create([
                'access_level_id' => $accessLevelId,
                'event_id' => $eventId,
                'organiser_id' => $event->organiser_id,
                'ref' => Str::random('8'),
                'email' => $email,
                'answers' => $answers
            ]);

            DB::commit();

            $settings = optional($attendee->accessLevel)->generalSettings;

            Mail::to($email)->later(now()->addSeconds(5), new AttendeeMail($settings, $lang));

            $message = $lang === 'arabic' ? optional($settings)->success_message_arabic ?: 'Saved successfully' : (optional($settings)->success_message ?: 'Saved successfully');

            return $this->view(
                data: ['message' => $message],
                component: "/form/{$accessLevelId}/success?lang=$lang", returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            \Log::error($th);

            $message = 'An error occurred while submitting the form.';
            return $this->view(
                data: ['message' => $message],
                flashMessage: $message, messageType: 'danger',
                component: "/form/{$accessLevelId}?lang=$lang", returnType: 'redirect'
            );
        }
    }

    public function approveAttendee(string $attendeeId, int $status, ?string $eventId = null)
    {
        $attendee = $this->find($attendeeId);
        $attendee->load(['accessLevel.generalSettings']);

        $attendee->update([
            'status' => $status
        ]);

        if ($status == 1) {
            $this->sendApprovalEmailToAttendees([$attendee]);
        }

        $message = 'Attendee has been ' . ($status === 1 ? 'approved' : ($status === 2 ? 'declined' : 'reinstated'));
        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route, returnType: 'redirect'
        );
    }

    public function bulkApproveAttendee(array $attendeeIds, int $status, ?string $eventId = null)
    {
        $this->model->query()
            ->whereIn('id', $attendeeIds)
            ->update(['status' => $status]);

        $attendees = $this->model->query()
            ->with('accessLevel.generalSettings')
            ->whereIn('id', $attendeeIds)
            ->get();

        if ($status == 1) {
            $this->sendApprovalEmailToAttendees($attendees);
        }

        $message = 'Attendees has been ' . ($status === 1 ? 'approved' : ($status === 2 ? 'declined' : 'reinstated'));
        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route, returnType: 'redirect'
        );
    }

    private function sendApprovalEmailToAttendees($attendees): void
    {
        foreach ($attendees as $attendee) {

            $settings = optional($attendee->accessLevel)->generalSettings;

            Mail::to($attendee->email)
                ->later(now()->addSeconds(5), new ApprovalMail($settings));
        }
    }

    public function sendMessage(array $data, string $attendeeId, ?string $eventId = null)
    {
        $attendee = $this->find($attendeeId);

        Mail::to($attendee->email)->later(now()->addSeconds(3), new CustomAttendeeMail($data));

        $message = 'Email sent successfully';

        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route, returnType: 'redirect'
        );
    }

    public function assignZones(array $zones, string $attendeeId, ?string $eventId = null)
    {
        $attendee = $this->find($attendeeId);

        $attendee->zones()->delete();

        $zones = collect($zones)->map(fn($zone) => [
            'id' => Str::uuid(),
            'attendee_id' => $attendeeId,
            'zone_id' => $zone,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('attendee_zones')->insert($zones->toArray());

        $message = 'Zones has been assigned to attendee';
        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route, returnType: 'redirect'
        );
    }

    public function bulkAssignZones(array $attendeeIds, array $zones, ?string $eventId = null)
    {
        $attendees = $this->model->query()
            ->with('zones')
            ->whereIn('id', $attendeeIds)
            ->get();

        foreach ($attendees as $attendee) {
            $attendee->zones()->delete();

            foreach ($zones as $zone) {
                AttendeeZone::create([
                    'attendee_id' => $attendee->id,
                    'zone_id' => $zone,
                ]);
            }
        }

        $message = 'Zones has been assigned to attendees';
        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route, returnType: 'redirect'
        );
    }

    public function sendInvitation(string $attendeeId)
    {
        $attendee = $this->find($attendeeId);
        $eventId = $attendee->event_id;

        $surveyLink = config('app.url') . '/e/' . $eventId . '/a/' . $attendee->access_level_id;
        $settings = $attendee->accessLevel->generalSettings;

        $this->sendInvitationMail($attendee, $settings, $surveyLink);

        $message = 'Invitation Link has been sent';
        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route, returnType: 'redirect'
        );
    }

    public function sendBulkInvitations(array $attendeeIds, ?string $event_id = null)
    {
        $attendees = $this->model->query()
            ->whereIn('id', $attendeeIds)
            ->get();

        foreach ($attendees as $attendee) {
            $eventId = $attendee->event_id;

            $surveyLink = config('app.url') . '/e/' . $eventId . '/a/' . $attendee->access_level_id;
            $settings = $attendee->accessLevel->generalSettings;

            $this->sendInvitationMail($attendee, $settings, $surveyLink);
        }

        $message = 'Invitation Link has been sent to the attendees';
        $route = $event_id ? "/event/$event_id/attendees" : "/attendees";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route, returnType: 'redirect'
        );
    }

    private function sendInvitationMail($attendee, $settings, $surveyLink): void
    {
        Mail::to($attendee->email)
            ->later(now()->addSeconds(5), new InvitationMail($settings, $surveyLink));
    }
}
