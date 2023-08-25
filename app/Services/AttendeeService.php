<?php

namespace App\Services;

use App\Exports\ExportAttendees;
use App\Helpers\QRCodeHelper;
use App\Http\Resources\AttendeeExportResource;
use App\Mail\ApprovalMail;
use App\Mail\AttendeeMail;
use App\Mail\CustomAttendeeMail;
use App\Mail\InvitationMail;
use App\Models\Area;
use App\Models\Attendee;
use App\Models\AttendeeArea;
use App\Models\AttendeeZone;
use App\Models\BadgesArea;
use App\Models\BadgesZone;
use App\Models\Invite;
use App\Models\Zone;
use App\Repositories\BaseRepository;
use App\Services\traits\HasFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Maatwebsite\Excel\Facades\Excel;

class AttendeeService extends BaseRepository
{
    use HasFile;

    protected string $images_path;

    public function __construct(Attendee $model, public FileService $file, public EventService $eventService, public AccessLevelsService $accessLevelsService, private AccountEventAccessService $accountEventAccessService)
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
            ->when($request->input('q'), function ($query) use ($request) {
                $searchTerm = $request->q;
                $query->where('email', 'like', "%{$searchTerm}%")
                    ->orWhere('ref', 'like', "%{$searchTerm}%")
                    ->orWhere('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('last_name', 'like', "%{$searchTerm}%")
                    ->orWhereHas('event', function ($q) use ($searchTerm) {
                        $q->where('title', 'like', "%{$searchTerm}%")
                            ->orWhere('title_arabic', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('accessLevel', function ($q) use ($searchTerm) {
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
                    case 'status':
                        $query->orderBy('status');
                        break;
                    default:
                        $query->orderByDesc('created_at');
                }
            })
            ->when(($request->input('filter') || $request->filter == '0'), function ($query) use ($request) {
                $query->whereStatus($request->filter);
            })
            ->latest()
            ->paginate($request->per_page ?: 10)
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
                if ($answer['title'] == 'Email Address') {
                    $email = $answer['answer'];
                }
                if ($answer['title'] == 'First Name') {
                    $first_name = $answer['answer'];
                }
                if ($answer['title'] == 'Last Name') {
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

    public function approveAttendee(string $attendeeId, int $status, ?string $eventId = null)
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
        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
        );
    }

    public function bulkApproveAttendee(array $attendeeIds, int $status, ?string $eventId = null)
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
        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
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

            $qr = QRCodeHelper::getQRCode($attendee->ref, 'png');

            $path = $this->uploadBase64File(file_get_contents($qr));
            $qrPath = Storage::disk(config('filesystems.default'))->url($path);

            Mail::to($attendee->email)
                ->later(now()->addSeconds(5), new ApprovalMail($settings, $attendee->event->organiser, $qrPath, $attendee->first_name));
        }
    }

    public function sendMessage(array $data, string $attendeeId, ?string $eventId = null)
    {
        $attendee = $this->find($attendeeId);

        Mail::to($attendee->email)->later(now()->addSeconds(3), new CustomAttendeeMail($data, $attendee->event->organiser));

        $message = 'Email sent successfully';

        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
        );
    }

    public function assignZones(array $zones, string $attendeeId, ?string $eventId = null)
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
        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
        );
    }

    public function assignAreas(array $areas, string $attendeeId, ?string $eventId = null)
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
        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
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
            component: $route,
            returnType: 'redirect'
        );
    }

    public function bulkAssignAreas(array $attendeeIds, array $areas, ?string $eventId = null)
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
        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
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

    public function downloadAttendeeBadge(Request $request, string $attendeeId, string $badgeId, ?string $eventId = null)
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
                if (in_array(strtolower(str_replace(' ', '_', $question['question'])), ['personal_photo', 'personal_picture', 'bhhgggg']) && $answer = $question['answer']) {
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
        $badgeDatas[] = (object)['column_title' => 'full_name', 'column_value' => $attendee->first_name . str_repeat('&nbsp;', 1) . $attendee->last_name];


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

        $data = ['html_data' => $html_data, 'badge' => $badge, 'type' => $request->type, 'downloads' => $attendee->downloads, 'downloaded' => $attendee->printed, 'collected' => $attendee->collected];

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

    public function uploadAttendees(string $eventId, array $attendees, string $accessLevelId, bool $approve, bool $mail)
    {
        $organiserId = auth()->user()->activeOrganiser();
        $accessLevel = $this->accessLevelsService->find($accessLevelId);
        $surveyLink = config('app.url') . '/a/' . $accessLevelId;
        $settings = $accessLevel->generalSettings;

        $organiser = $accessLevel->event->organiser;

        foreach ($attendees as $attendee) {
            $email = $attendee['Email Address'];
            $first_name = $attendee['First Name'];
            $last_name = $attendee['Last Name'];
            $ref = Str::random('8');

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

            if ($mail) {
                Mail::to($email)->later(now()->addSeconds(3), new InvitationMail(
                    settings: $settings,
                    surveyLink: "$surveyLink?ref=$inviteId",
                    organiser: $organiser,
                    firstName: $first_name,
                    lastName: $last_name,
                    registration: $accessLevel->registration,
                    ref: $ref
                ));
            }
        }

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
            $attendee = $this->model->query()
                ->whereRef($attendeeRef)
                ->with(['accessLevel', 'event'])
                ->firstOrFail();

            return $this->view(
                data: $attendee,
                flashMessage: 'Attendee fetched',
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

    public function togglePrinted(array $attendee_ids, bool $printed = true, ?string $eventId = null)
    {
        $this->model->query()
            ->whereIn('id', $attendee_ids)
            ->update([
                'printed' => $printed
            ]);

        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
        $message = (count($attendee_ids) > 1 ? "Attendees" : "Attendee") . " print status updated.";

        return $this->view(
            data: ['message' => $message],
            flashMessage: $message,
            component: $route,
            returnType: 'redirect'
        );
    }

    public function toggleCollected(array $attendee_ids, bool $collected, ?string $eventId = null)
    {
        $this->model->query()
            ->whereIn('id', $attendee_ids)
            ->update([
                'collected' => $collected
            ]);

        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
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

    public function changeAccessLevel(string $eventId, string $attendeeId, string $access_level_id)
    {
        $this->update([
            'access_level_id' => $access_level_id
        ], $attendeeId);

        $message = 'Attendee has been moved to access level.';

        $route = "/event/$eventId/attendees";

        return $this->view(data: ['message' => $message], flashMessage: $message, component: $route, returnType: 'redirect');
    }

    public function deleteAttendee(string $attendeeId, ?string $eventId = null)
    {
        $this->delete($attendeeId);

        $route = $eventId ? "/event/$eventId/attendees" : "/attendees";
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

        if ($surveysQuery) {
            $surveys = $surveysQuery->whereNot('title', 'Email Address')
                ->whereNot('title', 'First Name')
                ->whereNot('title', 'Last Name')
                ->get()
                ->map(fn ($survey) => $survey->title);

            $attendees = $this->model->query()
                ->with('event')
                ->when($active_organiser, function ($query) use ($active_organiser) {
                    $query->whereOrganiserId($active_organiser);
                })
                ->whereEventId($eventId)
                ->whereAccessLevelId($accessLevelId)
                ->latest()
                ->get()
                ->map(function ($attendee) {
                    $answers = [];

                    foreach ($attendee->answers as $answer) {
                        if ($answer['question'] != 'Email Address' && $answer['question'] != 'First Name' && $answer['question'] != 'Last Name') {
                            $value = $answer['answer'];
                            $answers[] = is_array($value) ? join(', ', $value) : $value;
                        }
                    }

                    return [
                        'event_title' => $attendee->event->title,
                        'first_name' => $attendee->first_name,
                        'last_name' => $attendee->last_name,
                        'email' => $attendee->email,
                        'reference' => $attendee->ref,
                        'downloads' => $attendee->downloads ?: 0,
                        'date_created' => $attendee->created_at->format('Y/M/d h:i A'),
                        'printed' => $attendee->printed ? 'printed' : 'not printed',
                        'collected' => $attendee->collected ? 'collected' : 'not collected',
                        ...$answers
                    ];
                });
        }



        return new ExportAttendees(event_id: $eventId, event_ids: auth()->user()->userEventAccessId(), attendees: $attendees, questions: count($surveys) > 0 ? $surveys->toArray() : []);
    }

    public function PSPDFKit()
    {

        $FileHandle = fopen('result.pdf', 'w+');

        $curl = curl_init();

        $instructions = '{
            "parts": [
              {
                "html": "index.html",
                "pages": {"pageCount": 0},
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
}
