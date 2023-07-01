<?php

namespace App\Services;

use App\Models\Attendee;
use App\Repositories\BaseRepository;
use App\Services\traits\HasFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->with(['event', 'accessLevel'])
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
                    'date_submitted' => $attendee->created_at->format('jS M, Y H:i')
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

            $this->create([
                'access_level_id' => $accessLevelId,
                'event_id' => $eventId,
                'organiser_id' => $event->organiser_id,
                'ref' => Str::random('8'),
                'email' => $email,
                'answers' => $answers
            ]);

            DB::commit();

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

    public function approveAttendee(string $attendeeId, ?string $eventId = null)
    {
        $attendee = $this->find($attendeeId);
        $attendee->update([
            'accept_status' => Attendee::ACCEPT_STATUS['ACCEPTED']
        ]);

        $email = $attendee->email;
        $generalSettings = $attendee->accessLevel->generalSettings;

        // TODO: Send approval email to the $attendee->email if status is 1.
        // Email template can be found in access level $generalSettings->approval_message_title && $approval_message.

        $message = 'Attendee has been accepted.';
        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route, returnType: 'redirect'
        );
    }
}
