<?php

namespace App\Services;

use App\Models\Attendee;
use App\Repositories\BaseRepository;
use App\Services\traits\HasFile;
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
                    $answers[] = ['question' => $answer['question'], 'answer' => Storage::disk(config('filesystems.default'))->url($fileUrl)];
                } else {
                    $answers[] = ['question' => $answer['question'], 'answer' => $answer['answer'] ?? ''];
                }
            }

            $this->create([
                'access_level_id' => $accessLevelId,
                'event_id' => $eventId,
                'organiser_id' => $event->organiser_id,
                'ref' => Str::random('8'),
                'email' => $email,
                'answers' => json_encode($answers)
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

}
