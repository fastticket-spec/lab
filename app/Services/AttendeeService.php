<?php

namespace App\Services;

use App\Exports\ExportAttendees;
use App\Exports\ExportCheckins;
use App\Helpers\QRCodeHelper;
use App\Http\Resources\AttendeeExportResource;
use App\Http\Resources\CheckinAttendeeResource;
use App\Mail\ApprovalMail;
use App\Mail\AttendeeMail;
use App\Mail\CustomAttendeeMail;
use App\Mail\InvitationMail;
use App\Models\Area;
use App\Models\Attendee;
use App\Models\AttendeeArea;
use App\Models\AttendeeCheckIn;
use App\Models\AttendeeZone;
use App\Models\BadgesArea;
use App\Models\BadgesZone;
use App\Models\Invite;
use App\Models\Zone;
use App\Repositories\BaseRepository;
use App\Services\traits\HasFile;
use AshAllenDesign\ShortURL\Exceptions\ShortURLException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AttendeeService extends BaseRepository
{
    use HasFile;

    protected string $images_path;

    public function __construct(Attendee $model, public FileService $file, public EventService $eventService, public AccessLevelsService $accessLevelsService, private AccountEventAccessService $accountEventAccessService, public WhatsappService $whatsappService)
    {
        parent::__construct($model);

        $this->images_path = config('filesystems.directory') . "accreditation_images/";
    }

    public function fetchAttendees(Request $request, ?string $eventId = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $account = auth()->user()->account;
        $roleId = $account->role_id;

        $eventsAccessID = null;
        if ($roleId) {
            $eventsAccessID = $this->accountEventAccessService->findBy(['account_id' => $account->id])->map(fn ($access) => $access->event_id);
        }


        return $this->model->query()
            ->with(['event', 'accessLevel.accessLevelBadge.badge', 'zones.zone'])
            ->when($account->active_organiser, function ($query) use ($account) {
                $query->where('organiser_id', $account->active_organiser);
            })
            ->when($eventId, function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            })
            ->when($eventsAccessID, function ($query) use ($eventsAccessID) {
                $query->whereIn('event_id', $eventsAccessID);
            })
            ->when($request->input('q'), function ($query) use ($request, $eventId) {
                $searchTerm = $request->q;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where(DB::raw('lower(email)'), 'like', strtolower("%{$searchTerm}%"))
                        ->orWhere(DB::raw('lower(ref)'), 'like', strtolower("%{$searchTerm}%"))
                        ->orWhere(DB::raw('lower(first_name)'), 'like', strtolower("%{$searchTerm}%"))
                        ->orWhere(DB::raw('lower(last_name)'), 'like', strtolower("%{$searchTerm}%"))
                        ->orWhere(DB::raw('lower(answers)'), 'like', strtolower("%{$searchTerm}%"))
                        ->orWhereHas('event', function ($q) use ($searchTerm) {
                            $q->where(DB::raw('lower(title)'), 'like', strtolower("%{$searchTerm}%"))
                                ->orWhere(DB::raw('lower(title_arabic)'), 'like', strtolower("%{$searchTerm}%"));
                        })
                        ->orWhereHas('accessLevel', function ($q) use ($searchTerm) {
                            $q->where(DB::raw('lower(title)'), 'like', strtolower("%{$searchTerm}%"))
                                ->orWhere(DB::raw('lower(title_arabic)'), 'like', strtolower("%{$searchTerm}%"));
                        });
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
                    case 'status':
                        $query->orderBy('status');
                        break;
                    default:
                        $query->orderByDesc('created_at');
                }
            })
            ->when(($request->input('filter') || $request->filter == '0'), function ($query) use ($request) {

                switch ($request->filter) {
                    case '3':
                        $query->wherePrinted(1);
                        break;
                    case '4':
                        $query->wherePrinted(0)->whereStatus(1);
                        break;
                    default:
                        $query->whereStatus($request->filter);
                }
            })

            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(function ($attendee) {
                return [
                    'id' => $attendee->id,
                    'access_level' => $attendee->accessLevel,
                    'category' => $attendee->event,
                    'ref' => $attendee->ref,
                    'answers' => $attendee->answers,
                    'first_name' => $attendee->first_name,
                    'last_name' => $attendee->last_name,
                    'email' => $attendee->email,
                    'status' => Attendee::STATUS_READABLE[$attendee->status],
                    'accept_status' => Attendee::ACCEPT_STATUS_READABLE[$attendee->accept_status],
                    'date_submitted' => $attendee->created_at->format('jS M, Y H:i'),
                    'zones' => $attendee->zones->map(fn ($zone) => $zone->zone_id),
                    'areas' => $attendee->areas->map(fn ($area) => $area->area_id),
                    'badge' => optional($attendee->accessLevel->accessLevelBadge)->badge,
                    'printed' => !!$attendee->printed,
                    'collected' => !!$attendee->collected,
                    'downloads' => $attendee->downloads
                ];
            });
    }

    public function createAttendee(Request $request, string $eventId, string $accessLevelId)
    {
        $lang = $request->lang;
        $ref = $request->reference;

        $event = $this->eventService->find($eventId);

        try {
            DB::beginTransaction();

            $email = '';
            $first_name = '';
            $last_name = '';

            $answers = [];
            foreach ($request->answers as $answer) {
                if (!$email && $answer['title'] == 'Email Address') {
                    $email = $answer['answer'];
                }
                if (!$first_name && $answer['title'] == 'First Name') {
                    $first_name = $answer['answer'];
                }
                if (!$last_name && $answer['title'] == 'Last Name') {
                    $last_name = $answer['answer'];
                }
                if ($answer['type'] == '4' && ($file = $answer['answer'])) {
                    $fileUrl = $this->uploadFile($file, $answer['question'], '-accreditation-file-');
                    $answers[] = ['type' => $answer['type'], 'question' => $answer['question'], 'answer' => Storage::disk(config('filesystems.default'))->url($fileUrl)];
                } elseif ($answer['type'] == '12') {
                    $answers[] = ['type' => $answer['type'], 'question' => $answer['question'], 'answer' => $answer['country_code'] . '-' . $answer['answer'] ?? ''];
                } else {
                    $answers[] = ['type' => $answer['type'], 'question' => $answer['question'], 'answer' => $answer['answer'] ?? ''];
                }
            }

            if ($ref && ($attendee = $this->findOneBy(['ref' => $ref]))) {
                $answersCollection = collect($answers);
                foreach ($attendee->answers as $answer) {
                    if (!($answersCollection->search(function ($a) use ($answer) {
                        return $a['question'] == $answer['question'];
                    }))) {
                        $answersCollection[] = $answer;
                    }
                }

                $attendee->update(['email' => $email, 'first_name' => $first_name, 'last_name' => $last_name, 'answers' => $answersCollection->toArray()]);
            } else {
                $attendee = $this->create([
                    'access_level_id' => $accessLevelId,
                    'event_id' => $eventId,
                    'organiser_id' => $event->organiser_id,
                    'ref' => $ref ?: Str::random('8'),
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'answers' => $answers
                ]);
            }

            DB::commit();

            $settings = optional($attendee->accessLevel)->generalSettings;

            Mail::to($email)->later(now()->addSeconds(5), new AttendeeMail($settings, $lang, $attendee->event->organiser, $first_name));

            $message = $lang === 'arabic' ? optional($settings)->success_message_arabic ?: 'Saved successfully' : (optional($settings)->success_message ?: 'Saved successfully');

            $route = $request->route ? $request->route : "/form/{$accessLevelId}/success?lang=$lang";

            return $this->view(
                data: ['message' => $message],
                component: $route,
                returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            \Log::error($th);

            $message = 'An error occurred while submitting the form.';
            $route = $request->route ? $request->route : "/form/{$accessLevelId}?lang=$lang";

            return $this->view(
                data: ['message' => $message],
                flashMessage: $message,
                messageType: 'danger',
                component: $route,
                returnType: 'redirect'
            );
        }
    }

    public function approveAttendee(string $attendeeId, int $status, ?string $eventId = null, ?int $page = 1)
    {
        $attendee = $this->find($attendeeId);
        $attendee->load(['accessLevel.generalSettings', 'event.organiser']);

        $attendee->update([
            'status' => $status
        ]);

        if ($status == 1) {
            $this->sendApprovalEmailToAttendees([$attendee]);
        }

        $message = 'Attendee has been ' . ($status === 1 ? 'approved' : ($status === 2 ? 'declined' : 'reinstated'));
        $route = $eventId ? "/event/$eventId/attendees?page=$page" : "/attendees?page=$page";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
        );
    }

    public function bulkApproveAttendee(array $attendeeIds, int $status, ?string $eventId = null, ?int $page = 1)
    {
        $this->model->query()
            ->whereIn('id', $attendeeIds)
            ->update(['status' => $status]);

        $attendees = $this->model->query()
            ->with(['accessLevel.generalSettings', 'event.organiser'])
            ->whereIn('id', $attendeeIds)
            ->get();

        if ($status == 1) {
            $this->sendApprovalEmailToAttendees($attendees);
        }

        $message = 'Attendees has been ' . ($status === 1 ? 'approved' : ($status === 2 ? 'declined' : 'reinstated'));
        $route = $eventId ? "/event/$eventId/attendees?page=$page" : "/attendees?page=$page";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
        );
    }

    private function sendApprovalEmailToAttendees($attendees): void
    {
        foreach ($attendees as $attendee) {

            $settings = optional($attendee->accessLevel)->generalSettings;
            $mobileNumberArray = collect($attendee->answers)->firstWhere('question', 'Mobile Number');
            $phone = $mobileNumberArray ? $mobileNumberArray['answer'] : '';

            $ref = $attendee->ref;
            $qrContent = $ref;
            if ($settings && $settings->enable_vcard) {
                $qrContent = "BEGIN:VCARD\nVERSION:3.0\nN:$attendee->last_name;$attendee->first_name\nFN:$attendee->first_name $attendee->last_name\nORG:\nTITLE:\nADR:\nTEL;WORK;VOICE:$phone\nTEL;FAX:\nEMAIL;WORK;INTERNET:$attendee->email\nURL:\nNOTE:$ref\nEND:VCARD";
            }

            $qr = QRCodeHelper::getQRCode($qrContent, 'png');

            $path = $this->uploadBase64File(file_get_contents($qr), $ref);
            $qrPath = Storage::disk(config('filesystems.default'))->url($path);

            Mail::to($attendee->email)
                ->later(now()->addSeconds(5), new ApprovalMail($settings, $attendee->event->organiser, $qrPath, $attendee->first_name, $ref));
        }
    }

    public function sendMessage(array $data, string $attendeeId, ?string $eventId = null)
    {
        $attendee = $this->find($attendeeId);

        Mail::to($attendee->email)->later(now()->addSeconds(3), new CustomAttendeeMail($data, $attendee->event->organiser));

        $message = 'Email sent successfully';

        $page = $data['page'] ?? 1;

        $route = $eventId ? "/event/$eventId/attendees?page=$page" : "/attendees?page=$page";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
        );
    }

    public function assignZones(array $zones, string $attendeeId, ?string $eventId = null, ?int $page = 1)
    {
        $attendee = $this->find($attendeeId);

        $attendee->zones()->delete();

        $zones = collect($zones)->map(fn ($zone) => [
            'id' => Str::uuid(),
            'attendee_id' => $attendeeId,
            'zone_id' => $zone,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('attendee_zones')->insert($zones->toArray());

        $message = 'Zones has been assigned to attendee';
        $route = $eventId ? "/event/$eventId/attendees?page=$page" : "/attendees?page=$page";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
        );
    }

    public function assignAreas(array $areas, string $attendeeId, ?string $eventId = null, ?int $page = 1)
    {
        $attendee = $this->find($attendeeId);

        $attendee->areas()->delete();

        $areas = collect($areas)->map(fn ($area) => [
            'id' => Str::uuid(),
            'attendee_id' => $attendeeId,
            'area_id' => $area,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('attendee_areas')->insert($areas->toArray());

        $message = 'Areas has been assigned to attendee';
        $route = $eventId ? "/event/$eventId/attendees?page=$page" : "/attendees?page=$page";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
        );
    }

    public function bulkAssignZones(array $attendeeIds, array $zones, ?string $eventId = null, ?int $page = 1)
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
        $route = $eventId ? "/event/$eventId/attendees?page=$page" : "/attendees?page=$page";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
        );
    }

    public function bulkAssignAreas(array $attendeeIds, array $areas, ?string $eventId = null, ?int $page = 1)
    {
        $attendees = $this->model->query()
            ->with('areas')
            ->whereIn('id', $attendeeIds)
            ->get();

        foreach ($attendees as $attendee) {
            $attendee->areas()->delete();

            foreach ($areas as $area) {
                AttendeeArea::create([
                    'attendee_id' => $attendee->id,
                    'area_id' => $area,
                ]);
            }
        }

        $message = 'Area has been assigned to attendees';
        $route = $eventId ? "/event/$eventId/attendees?page=$page" : "/attendees?page=$page";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
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
            component: $route,
            returnType: 'redirect'
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
            component: $route,
            returnType: 'redirect'
        );
    }

    private function sendInvitationMail($attendee, $settings, $surveyLink): void
    {
        //        Mail::to($attendee->email)
        //            ->later(now()->addSeconds(5), new InvitationMail($settings, $surveyLink));
    }

    public function updateAnswer(Request $request, string $attendeeId, ?string $eventId = null)
    {
        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";

        try {
            DB::beginTransaction();

            $attendee = $this->find($attendeeId);
            $email = '';
            $first_name = '';
            $last_name = '';

            $answers = [];
            foreach ($request->answers as $answer) {
                if ($answer['question'] == 'Email Address') {
                    $email = $answer['answer'];
                }
                if ($answer['question'] == 'First Name') {
                    $first_name = $answer['answer'];
                }
                if ($answer['question'] == 'Last Name') {
                    $last_name = $answer['answer'];
                }
                $answers[] = ['type' => $answer['type'], 'question' => $answer['question'], 'answer' => $answer['answer'] ?? ''];
            }

            $attendee->update([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'answers' => $answers
            ]);

            DB::commit();

            $attendee->refresh();

            $message = 'Answer updated successfully!';

            return $this->view(
                data: ['attendee' => $attendee, 'message' => $message]
            );
        } catch (\Throwable $th) {
            \Log::error($th);

            $message = 'An error occurred while updating answer!.';
            return $this->view(
                data: ['message' => $message],
                flashMessage: $message,
                messageType: 'danger',
                component: $route,
                returnType: 'redirect'
            );
        }
    }

    public function downloadAttendeeBadge($type, string $attendeeId, string $badgeId, ?string $eventId = null)
    {
        $attendee = $this->find($attendeeId);
        $event = $attendee->event;
        $getBadge = $event->eventBadges()->whereBadgeId($badgeId)->first();
        $badge = $event->badges()->whereId($badgeId)->first();
        $badgeColumn = $badge->badgeColumns;


        //        $badgeColumn = BadgeColumn::where('badge_id', $badgeId)->get();
        //        $badge = Badges::where('id', $badgeId)->first();
        //        $getBadge = Event_badge::where('badge_id', $badgeId)->first();
        //        $badgeDatas = BadgeData::where('event_id', $eventId)->where('badge_id', $badgeId)->where('attendee_id', $attendeeId)->get()->toArray() ?? [];
        //        $surveyAnswer = $attendee->answers;
        $badgeDatas = [];
        $survey = $attendee->answers;
        //        dd($survey);


        foreach ($badgeColumn as $k => $col) {
            foreach ($survey as $i => $question) {

                if (in_array(strtolower(str_replace(' ', '_', $question['question'])), ['personal_photo', 'personal_picture', 'bhhgggg', 'persoal_photo']) && $answer = $question['answer']) {
                    $attendee->user_photo = $answer;
                } elseif ($answer = $question['answer']) {
                    $badgeDatas[] = (object)['column_title' => strtolower(str_replace(' ', '_', $question['question'])), 'column_value' => $answer];
                }
                //                if ($question->question_type_id != 8) {
                //                    $answer = QuestionAnswer::where('question_id', $question->id)->where('attendee_id', $attendee->id)->first();
                //                    if ($answer) {
                //                        // $badgeDatas[$k] = (object) [
                //                        //     'column_value' => $answer->answer_text,
                //                        //     'column_title' => strtolower(str_replace(' ', '_', $question->title))
                //                        // ];
                //
                //                        array_push($badgeDatas, (object)['column_title' => strtolower(str_replace(' ', '_', $question->title)), 'column_value' => $answer->answer_text]);
                //                    }
                //                }


                //                if ($question->question_type_id == 8) {
                //                    $answer = QuestionAnswer::where('question_id', $question->id)->where('attendee_id', $attendee->id)->first();
                //                    if ($answer) {
                //                        if (is_image(public_path() . '/user_content/' . $answer->answer_text)) {
                //                            $attendee->user_photo = public_path() . '/user_content/' . $answer->answer_text;
                //                        }
                //                    }
                //                }
            }
        }

        $badgeDatas[] = (object)['column_title' => 'function', 'column_value' => $event->title];
        //        $badgeDatas[] = (object)['column_title' => 'registration_reference', 'column_value' => $attendee->order->order_reference];
        $badgeDatas[] = (object)['column_title' => 'first_name', 'column_value' => $attendee->first_name];
        $badgeDatas[] = (object)['column_title' => 'last_name', 'column_value' => $attendee->last_name];
        $badgeDatas[] = (object)['column_title' => 'email', 'column_value' => $attendee->email];
        $badgeDatas[] = (object)['column_title' => 'full_name', 'column_value' => ($attendee->first_name . ' ' . $attendee->last_name)];


        //        if (!$attendee) {
        //            abort(404);
        //        }

        $badge_html = $getBadge->html;

        //        $path = config('attendize.event_images_path');
        $filename = $attendee->ref . '.png';
        $file_full_path = storage_path() . '/app/public/badge_qrs/' . $filename;
        //        if ($event->organiser_id == 53) {
        //            $mobile = $attendee->answers->wherein('question_id', 898, 896, 894, 891)->first()->answer_text;
        //            $d = "BEGIN:VCARD
        //            VERSION:4.0
        //            FN:$attendee->first_name  $attendee->last_name
        //            EMAIL;TYPE=work:$attendee->email
        //            TEL:
        //            END:VCARD";
        //            $d = "BEGIN:VCARD
        //VERSION:2.1
        //N:$attendee->first_name $attendee->last_name
        //EMAIL:$attendee->email
        //TEL;HOME;VOICE:$mobile
        //END:VCARD";
        //
        //            Card::encoding('UTF-8')->format('png')->generate($d, $file_full_path);
        //        } else {
        $qrCode = new QrCode($attendee->ref);
        $qrCode->setSize(300);
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setLabelFontSize(16);

        // dd($file_full_path);
        // Save it to a file
        $qrCode->writeFile($file_full_path);
        //        }
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($badge_html);
        $tags = $doc->getElementsByTagName("img");
        foreach ($tags as $tag) {
            if ($tag->getAttribute('class') === 'barcode') {

                $filelink = config('app.url') . '/storage/badge_qrs/' . $filename;
                $old_src = $tag->getAttribute('src');
                $type = pathinfo($filelink, PATHINFO_EXTENSION);
                $data = file_get_contents($filelink);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

                $tag->setAttribute('src', $base64);
                $tag->setAttribute('data-src', $old_src);
            }
            if ($tag->getAttribute('class') === 'user_photo' && !is_null($attendee->user_photo)) {
                $old_src = $tag->getAttribute('src');
                $new_src_url = $attendee->user_photo;

                $type = pathinfo($new_src_url, PATHINFO_EXTENSION);
                $data = file_get_contents($new_src_url);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                // $urlparts = parse_url($new_src_url);
                // $extracted = $urlparts['path'];
                $tag->setAttribute('src', $base64);
                $tag->setAttribute('data-src', $old_src);
            }
        }


        foreach ($badgeDatas as $data) {
            // dd($badgeDatas);
            foreach ($doc->getElementsByTagName('*') as $element) {
                if (!empty($element->getAttribute('key') && $element->getAttribute('key') == $data->column_title)) {
                    $element->nodeValue = $data->column_value;
                }

                if ($element->getAttribute('id') == 'order_ref') {
                    $element->nodeValue = $attendee->order->order_reference;
                }

                if (!empty($element->getAttribute('key')) && $element->getAttribute('key') == 'zone') {
                    $attXone = [];
                    $nodeId = $element->getAttribute('id');
                    foreach ($attendee->zones as $att_zone) {
                        $attXone[] = optional(BadgesZone::where('zone_id', $att_zone->zone_id)->first())->id;
                    }

                    //                    if (!in_array($element->getAttribute('id'), $attXone)) {
                    //                        $element->setAttribute('style', 'display: none;');
                    //                    }

                    if (!in_array($nodeId, $attXone)) {
                        $element->nodeValue = optional(Zone::find($nodeId))->zone;
                    }
                }

                if (!empty($element->getAttribute('key')) && $element->getAttribute('key') == 'area') {
                    $attXone = [];
                    $nodeId = $element->getAttribute('id');
                    foreach ($attendee->areas as $att_area) {
                        $attXone[] = optional(BadgesArea::where('area_id', $att_area->area_id)->first())->id;
                    }

                    if (!in_array($nodeId, $attXone)) {
                        $element->nodeValue = optional(Area::find($nodeId))->area;
                    }

                    //                    if (!in_array($element->getAttribute('id'), $attXone)) {
                    //                        $element->nodeValue = $area;
                    //                    }
                }
            }
        }

        $html_data = $doc->saveHTML();

        $data = ['html_data' => $html_data, 'badge' => $badge, 'type' => $type, 'downloads' => $attendee->downloads, 'downloaded' => $attendee->printed, 'collected' => $attendee->collected];

        return response()->json($data);

        return view('badge_display', $data);
    }

    public function count(bool $all = false, ?bool $approved = false, ?bool $declined = false, ?string $eventId = null, array|Collection|null $allowedEventIds = null): int
    {
        if ($all) return $this->model->query()->count();

        $user = auth()->user();
        $activeOrganiser = $user->activeOrganiser();

        return $this->model->query()
            ->when(!$activeOrganiser, function ($query) use ($user) {
                $query->whereIn('organiser_id', $user->organiserIds());
            })
            ->when($activeOrganiser, function ($query) use ($activeOrganiser) {
                $query->where('organiser_id', $activeOrganiser);
            })
            ->when($approved, function ($query) {
                $query->where('status', Attendee::STATUS['APPROVED']);
            })
            ->when($declined, function ($query) {
                $query->where('status', Attendee::STATUS['DECLINED']);
            })
            ->when($eventId, function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            })
            ->when($allowedEventIds, function ($query) use ($allowedEventIds) {
                $query->whereIn('event_id', $allowedEventIds);
            })
            ->count();
    }

    public function countDownloads(?string $eventId = null, array|Collection|null $allowedEventIds = null): int
    {
        $user = auth()->user();
        $activeOrganiser = $user->activeOrganiser();

        return $this->model->query()
            ->when(!$activeOrganiser, function ($query) use ($user) {
                $query->whereIn('organiser_id', $user->organiserIds());
            })
            ->when($activeOrganiser, function ($query) use ($activeOrganiser) {
                $query->where('organiser_id', $activeOrganiser);
            })
            ->when($eventId, function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            })
            ->when($allowedEventIds, function ($query) use ($allowedEventIds) {
                $query->whereIn('event_id', $allowedEventIds);
            })
            ->sum('downloads');
    }

    /**
     * @throws ShortURLException
     */
    public function uploadAttendees(string $eventId, array $attendees, string $accessLevelId, bool $approve, bool $mail, bool $whatsapp)
    {
        DB::beginTransaction();
        $organiserId = auth()->user()->activeOrganiser();
        $accessLevel = $this->accessLevelsService->find($accessLevelId);
        $surveyLink = config('app.url') . '/a/' . $accessLevelId;
        $settings = $accessLevel->generalSettings;

        $organiser = $accessLevel->event->organiser;

        foreach ($attendees as $attendee) {
            $email = $attendee['Email Address'];
            $first_name = $attendee['First Name'];
            $last_name = $attendee['Last Name'];
            $phone = $attendee['Mobile Number'] ?? ($attendee['Phone Number'] ?? '');
            $ref = Str::random('8');

            if ($phone) {
                $phoneLength = strlen($phone);
                $phone = str_replace('+', '', $phone);

                if ($phoneLength == 9 || $phoneLength === 10) {
                    $phone = "966$phone";
                }
            }

            $answers = [];

            foreach ($attendee as $key => $value) {
                if ($key == 'email') {
                    $answers[] = ['type' => '5', 'answer' => $value, 'question' => $key];
                } else {
                    $answers[] = ['type' => '1', 'answer' => $value, 'question' => $key];
                }
            }

            $this->create([
                'access_level_id' => $accessLevelId,
                'organiser_id' => $organiserId,
                'event_id' => $eventId,
                'ref' => $ref,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'answers' => $answers,
                'status' => $approve,
                'accept_status' => $approve
            ]);

            $inviteId = Invite::create(['email' => $email, 'ref' => $ref, 'event_id' => $eventId, 'access_level_id' => $accessLevelId])->id;

            $surveyLink = "$surveyLink?ref=$inviteId";

            if ($mail) {
                $declineLink = '';
                if ($settings->decline_invitation) {
                    $declineLink = config('app.url') . "/decline-invite/$inviteId";
                }

                Mail::to($email)->later(now()->addSeconds(3), new InvitationMail(
                    settings: $settings,
                    surveyLink: $surveyLink,
                    organiser: $organiser,
                    firstName: $first_name,
                    lastName: $last_name,
                    registration: $accessLevel->registration,
                    ref: $ref,
                    declineLink: $declineLink
                ));
            }

            if ($whatsapp && $phone) {
                $surveyLink = (new URLShortenerService())->shorten($surveyLink);
                $this->whatsappService->sendMessage($phone, $surveyLink, "$first_name $last_name");
            }
        }

        DB::commit();

        $message = 'Attendees uploaded successfully!';

        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: "/event/$eventId/attendees",
            returnType: "redirect"
        );
    }

    public function checkAttendee(string $attendeeRef)
    {
        try {
            $eventAccessIds = auth()->user()->userEventAccessId();

            if (strlen($attendeeRef) > 20) {
                $attendeeRef = explode("\nEND:VCARD", explode('NOTE:', $attendeeRef)[1])[0];
                \Log::debug("vcard attendee ref $attendeeRef");
            }

            $attendee = $this->model->query()
                ->whereRef($attendeeRef)
                ->with(['accessLevel', 'event'])
                ->where('status', 1)
                ->whereHas('event', function ($query) use ($eventAccessIds) {
                    $query->whereIn('id', $eventAccessIds);
                })
                ->firstOrFail();

            if ($attendee->attendeeCheckins()->exists()) {
                return $this->view(
                    data: ['message' => 'Attendee already checked in.'],
                    statusCode: 400,
                    flashMessage: 'Attendee already checked in.',
                    component: '/dashboard',
                    returnType: 'redirect'
                );
            }

            $message = 'Attendee fetched';

            return $this->view(
                data: ['data' => new CheckinAttendeeResource($attendee), 'message' => $message],
                flashMessage: $message,
                component: '/dashboard',
                returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            if ($th instanceof ModelNotFoundException) {
                return $this->view(
                    data: ['message' => 'Attendee not found'],
                    statusCode: 400,
                    flashMessage: 'Attendee not found',
                    component: '/dashboard',
                    returnType: 'redirect'
                );
            }
            throw $th;
        }
    }

    public function checkinAttendee(string $attendeeRef)
    {
        try {
            $eventAccessIds = auth()->user()->userEventAccessId();

            if (strlen($attendeeRef) > 20 && Str::contains($attendeeRef, 'VCARD')) {
                $attendeeRef = explode("\nEND:VCARD", explode('NOTE:', $attendeeRef)[1])[0];
            }

            $attendee = $this->model->query()
                ->whereRef($attendeeRef)
                ->with(['accessLevel', 'event'])
                ->where('status', 1)
                ->whereHas('event', function ($query) use ($eventAccessIds) {
                    $query->whereIn('id', $eventAccessIds);
                })
                ->firstOrFail();

            // if ($attendee->attendeeCheckins()->exists()) {
            //     return $this->view(
            //         data: ['message' => 'Attendee already checked in.'],
            //         statusCode: 400,
            //         flashMessage: 'Attendee already checked in.',
            //         component: '/dashboard',
            //         returnType: 'redirect'
            //     );
            // }

            $attendee->checkinAttendee();

            return $this->view(
                data: ['message' => 'Checked in successfully', 'attendee' => $attendee->answers, 'checked_in_by' => auth()->user()->first_name . ' ' . auth()->user()->last_name],
                flashMessage: 'Checked in successfully',
                component: '/dashboard',
                returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            if ($th instanceof ModelNotFoundException) {
                return $this->view(
                    data: ['message' => 'Reference not found'],
                    statusCode: 400,
                    flashMessage: 'Reference not found',
                    component: '/dashboard',
                    returnType: 'redirect'
                );
            }
            throw $th;
        }
    }

    public function checkoutAttendee(string $attendeeRef)
    {
        try {
            $eventAccessIds = auth()->user()->userEventAccessId();

            $attendee = $this->model->query()
                ->whereRef($attendeeRef)
                ->with(['accessLevel', 'event'])
                ->where('status', 1)
                ->whereHas('event', function ($query) use ($eventAccessIds) {
                    $query->whereIn('id', $eventAccessIds);
                })
                ->firstOrFail();

            if (!$attendee->attendeeCheckins()->exists()) {
                return $this->view(
                    data: ['message' => 'Attendee has not been checked in yet.'],
                    statusCode: 400,
                    flashMessage: 'Attendee has not been checked in yet.',
                    component: '/dashboard',
                    returnType: 'redirect'
                );
            }

            $attendeeCheckin = $attendee->attendeeCheckins()->latest()->first();

            $attendeeCheckin->update(['checkout' => now()]);

            return $this->view(
                data: ['message' => 'Checked out successfully'],
                flashMessage: 'Checked out successfully',
                component: '/dashboard',
                returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            if ($th instanceof ModelNotFoundException) {
                return $this->view(
                    data: ['message' => 'Reference not found'],
                    statusCode: 400,
                    flashMessage: 'Reference not found',
                    component: '/dashboard',
                    returnType: 'redirect'
                );
            }
            throw $th;
        }
    }

    public function checkinAttendeeById(string $attendeeId, int $page, ?string $eventId = null)
    {
        $page = $eventId ? "/event/$eventId/attendees?page=$page" : "/attendees?page=$page";
        try {
            $attendee = $this->model->query()
                ->findOrFail($attendeeId);

            if ($attendee->attendeeCheckins()->exists()) {
                return $this->view(
                    data: ['message' => 'Attendee already checked in.'],
                    statusCode: 400,
                    flashMessage: 'Attendee already checked in.',
                    component: $page,
                    returnType: 'redirect'
                );
            }

            $attendee->checkinAttendee();

            return $this->view(
                data: ['message' => 'Checked in successfully', 'attendee' => $attendee->answers],
                flashMessage: 'Checked in successfully',
                component: $page,
                returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            if ($th instanceof ModelNotFoundException) {
                return $this->view(
                    data: ['message' => 'Attendee not found'],
                    statusCode: 400,
                    flashMessage: 'Reference not found',
                    component: $page,
                    returnType: 'redirect'
                );
            }
            throw $th;
        }
    }

    public function togglePrinted(array $attendee_ids, bool $printed = true, ?string $eventId = null, ?int $page = 1)
    {
        $this->model->query()
            ->whereIn('id', $attendee_ids)
            ->update([
                'printed' => $printed
            ]);

        $route = $eventId ? "/event/$eventId/attendees?page=$page" : "/attendees?page=$page";
        $message = (count($attendee_ids) > 1 ? "Attendees" : "Attendee") . " print status updated.";

        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
        );
    }

    public function toggleCollected(array $attendee_ids, bool $collected, ?string $eventId = null, ?int $page = 1)
    {
        $this->model->query()
            ->whereIn('id', $attendee_ids)
            ->update([
                'collected' => $collected
            ]);

        $route = $eventId ? "/event/$eventId/attendees?page=$page" : "/attendees?page=$page";
        $message = (count($attendee_ids) > 1 ? "Attendees" : "Attendee") . " collection status updated.";

        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
        );
    }

    public function incrementDownloads(string $attendeeId)
    {
        $attendee = $this->find($attendeeId);

        $attendee->update([
            'downloads' => $attendee->downloads + 1
        ]);

        return $this->view(
            data: ['message' => 'Updated downloads'],
        );
    }

    public function changeAccessLevel(string $eventId, string $attendeeId, string $access_level_id, ?int $page = 1, string $new_event_id)
    {
        $newAccessLevel = $this->accessLevelsService->find($access_level_id);
        $attendeesCount = $newAccessLevel->attendees()->count();

        $route = "/event/$eventId/attendees?page=$page";

        if (!$newAccessLevel->quantity_available || ($newAccessLevel->quantity_available > 0 && ($attendeesCount < $newAccessLevel->quantity_available))) {
            $this->update([
                'access_level_id' => $access_level_id,
                'event_id' => $new_event_id
            ], $attendeeId);

            $message = 'Attendee has been moved to access level.';

            return $this->view(data: ['message' => $message], flashMessage: $message, component: $route, returnType: 'redirect');
        }

        $message = 'The access level has been fully occupied!';
        return $this->view(data: ['message' => $message], statusCode: 400, flashMessage: $message, messageType: 'danger', component: $route, returnType: 'redirect');
    }

    public function deleteAttendee(string $attendeeId, ?string $eventId = null, ?int $page = 1)
    {
        $this->delete($attendeeId);

        $route = $eventId ? "/event/$eventId/attendees?page=$page" : "/attendees?page=$page";
        $message = 'Attendee deleted successfully!';

        return $this->view(data: ['message' => $message], flashMessage: $message, component: $route, returnType: 'redirect');
    }

    public function export(string $eventId, string $accessLevelId): ExportAttendees
    {
        $active_organiser = auth()->user()->account->active_organiser;

        $accessLevel = $this->accessLevelsService->find($accessLevelId);
        $surveys = [];
        $attendees = [];

        $surveysQuery = optional($accessLevel->surveyAccessLevels)
            ->surveys();

        if ($surveysQuery->exists()) {
            $surveys = $surveysQuery->whereNot('title', 'Email Address')
                ->get()
                ->map(fn ($survey) => $survey->title);

            $attendees = $this->model->query()
                ->with(['event', 'zones.zone'])
                ->when($active_organiser, function ($query) use ($active_organiser) {
                    $query->whereOrganiserId($active_organiser);
                })
                ->whereEventId($eventId)
                ->whereAccessLevelId($accessLevelId)
                ->latest()
                ->get()
                ->map(function ($attendee) {
                    $answers = [];
                    $firstName = '';
                    $lastName = '';

                    foreach ($attendee->answers as $answer) {
                        if (str_contains($answer['question'], 'First Name')) {
                            $firstName =   $firstName == '' ? $answer['answer'] : $firstName;
                        }

                        if (str_contains($answer['question'], 'Last Name')) {
                            $lastName =  $lastName == '' ?  $answer['answer'] : $lastName;
                        }


                        if ($answer['question'] != 'Email Address') {
                            $value = $answer['answer'];
                            $answers[] = is_array($value) ? join(', ', $value) : $value;
                        }
                    }

                    $attendeeZones = $attendee->zones->map(fn ($zone) => $zone->zone->zone)->toArray();

                    return [
                        'event_title' => $attendee->event->title,
                        'first_name' => is_null($attendee->first_name) || empty($attendee->first_name) ?  $firstName : $attendee->first_name,
                        'last_name' => is_null($attendee->last_name) || empty($attendee->last_name) ?  $lastName : $attendee->last_name,
                        'email' => $attendee->email,
                        'reference' => $attendee->ref,
                        'downloads' => $attendee->downloads ?: 0,
                        'date_created' => $attendee->created_at->format('Y/M/d h:i A'),
                        'printed' => $attendee->printed ? 'printed' : 'not printed',
                        'collected' => $attendee->collected ? 'collected' : 'not collected',
                        'zones' => join(', ', $attendeeZones),
                        ...$answers
                    ];
                });
        }



        return new ExportAttendees(event_id: $eventId, event_ids: auth()->user()->userEventAccessId(), attendees: $attendees, questions: count($surveys) > 0 ? $surveys->toArray() : []);
    }

    public function exportCheckins(string $eventId, string $accessLevelId): ExportCheckins
    {
        $active_organiser = auth()->user()->account->active_organiser;

        $attendees = $this->model->query()
            ->when($active_organiser, function ($query) use ($active_organiser) {
                $query->whereOrganiserId($active_organiser);
            })
            ->whereEventId($eventId)
            ->whereAccessLevelId($accessLevelId)
            ->pluck('id');

        $checkins = AttendeeCheckIn::whereIn('attendee_id', $attendees)
            ->with('attendee')
            ->latest()
            ->get()
            ->groupBy('attendee_id')
            ->values()
            ->map(function ($checkin) {
                $checkinUser = $checkin[0]->checkinUser;

                return [
                    'first_name' => $checkin[0]->attendee->first_name,
                    'last_name' => $checkin[0]->attendee->last_name,
                    'checkin_time' => $checkin[0]->created_at->format('d-M-Y H:i'),
                    'checkin_user' => optional($checkinUser)->first_name . ' ' . optional($checkinUser)->last_name,
                    'checkout_time' => $checkin[0]->checkout ? Carbon::createFromFormat('Y-m-d H:i:s', $checkin[0]->checkout)->format('d-M-Y H:i') : ''

                ];
            });

        return new ExportCheckins($checkins);
    }

    public function PSPDFKit()
    {

        $FileHandle = fopen('result.pdf', 'w+');

        $curl = curl_init();

        $instructions = '{
            "parts": [
              {
                "html": "index.html",
                "pages": {"pageCount": 1},
                "layout": {
                    "size": {
                      "width": 85,
                      "height": 120
                    }
                  }
              }
            ]
          }';

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.pspdfkit.com/build',
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_POSTFIELDS => array(
                'instructions' => $instructions,
                'index.html' => new \CURLFILE('/Users/avatechng/sa/easyticket/Accreditation-v3_renew/public/user_content/badges/testBadge.html')
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer pdf_live_BdFCjuj91grYWmMr0utk9iJpT2Tna5IOpEE67T1GDjM'
            ),
            CURLOPT_FILE => $FileHandle,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        fclose($FileHandle);
    }

    public function scanAttendee(string $attendeeRef)
    {
        try {
            $eventAccessIds = auth()->user()->userEventAccessId();

            if (strlen($attendeeRef) > 20 && Str::contains($attendeeRef, 'VCARD')) {
                $attendeeRef = explode("\nEND:VCARD", explode('NOTE:', $attendeeRef)[1])[0];
            }

            $attendee = $this->model->query()
                ->whereRef($attendeeRef)
                ->with(['accessLevel', 'event'])
                ->whereHas('event', function ($query) use ($eventAccessIds) {
                    $query->whereIn('id', $eventAccessIds);
                })
                ->firstOrFail();

            if ($attendee->attendeeScans()->exists()) {
                return $this->view(
                    data: ['message' => 'Attendee already scanned.'],
                    statusCode: 400,
                    flashMessage: 'Attendee already scanned in.',
                    component: '/dashboard',
                    returnType: 'redirect'
                );
            }

            $attendee->scanAttendee();

            return $this->view(
                data: ['message' => 'Attendee scanned successfully', 'scanned_by' => auth()->user()->first_name . ' ' . auth()->user()->last_name],
                flashMessage: 'Attendee scanned successfully',
                component: '/dashboard',
                returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            if ($th instanceof ModelNotFoundException) {
                return $this->view(
                    data: ['message' => 'Reference not found'],
                    statusCode: 400,
                    flashMessage: 'Reference not found',
                    component: '/dashboard',
                    returnType: 'redirect'
                );
            }
            throw $th;
        }
    }
}
