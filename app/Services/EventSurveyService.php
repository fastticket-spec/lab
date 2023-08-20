<?php

namespace App\Services;

use App\Models\EventSurvey;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventSurveyService extends BaseRepository
{
    public function __construct(EventSurvey $model)
    {
        parent::__construct($model);
    }

    public function fetchEventSurveys(Request $request, string $eventId)
    {
        return $this->model->query()
            ->with(['surveyAccessLevels.accessLevel', 'surveys'])
            ->whereEventId($eventId)
            ->latest()
            ->paginate($request->per_page ?: 10)
            ->withQueryString()
            ->through(function ($eventSurvey) {
                $accessLevels = $eventSurvey->surveyAccessLevels()
                    ->whereHas('accessLevel')
                    ->get()
                    ->map(fn($surveyAccessLevel) => $surveyAccessLevel->accessLevel->title);

                return [
                    'id' => $eventSurvey->id,
                    'name' => $eventSurvey->created_at->format('jS M, Y H:i'),
                    'access_levels' => $accessLevels,
                    'surveys' => $eventSurvey->surveys,
                    'status' => $eventSurvey->status
                ];
            });
    }

    public function createSurvey(Request $request, string $eventId)
    {
        DB::beginTransaction();

        $eventSurvey = $this->create(['event_id' => $eventId]);

        foreach ($request->access_levels as $access_level) {
            $eventSurvey->surveyAccessLevels()->create([
                'access_level_id' => $access_level
            ]);
        }

        foreach ($request->surveys as $survey) {
            $eventSurvey->surveys()->create([
                'event_id' => $eventId,
                'event_survey_id' => $eventSurvey->id,
                'title' => $survey['title'],
                'title_arabic' => $survey['title_arabic'] ?? '',
                'type' => $survey['type'],
                'options' => $survey['options'],
                'required' => $survey['required'],
            ]);
        }
        DB::commit();

        $message = 'Survey created successfully';

        return $this->view(data: ['message' => $message], flashMessage: $message, component: "/event/$eventId/event-surveys", returnType: 'redirect');
    }

    public function updateSurvey(Request $request, string $eventId, string $eventSurveyId)
    {
        DB::beginTransaction();

        $eventSurvey = $this->find($eventSurveyId);

        $eventSurvey->surveyAccessLevels()->delete();

        foreach ($request->access_levels as $access_level) {
            $eventSurvey->surveyAccessLevels()->create([
                'access_level_id' => $access_level
            ]);
        }

        if ($eventSurvey && $eventSurvey->surveys) {
            $eventSurvey->surveys()->delete();
        }

        foreach ($request->surveys as $survey) {
            $eventSurvey->surveys()->create([
                'event_id' => $eventId,
                'event_survey_id' => $eventSurvey->id,
                'title' => $survey['title'],
                'title_arabic' => $survey['title_arabic'] ?? '',
                'type' => $survey['type'],
                'options' => $survey['options'],
                'required' => $survey['required'] ?? false,
                'private' => $survey['private'] ?? false,
                'parent_index' => $survey['parent_index'] ?? null,
                'parent_answer' => $survey['parent_answer'] ?? null
            ]);
        }
        DB::commit();

        $message = 'Survey updated successfully';

        return $this->view(data: ['message' => $message], flashMessage: $message, component: "/event/$eventId/event-surveys", returnType: 'redirect');
    }

    public function updateStatus(string $eventId, string $eventSurveyId)
    {
        $route = "/event/$eventId/event-surveys";
        try {
            $eventSurvey = $this->find($eventSurveyId);
            $status = $eventSurvey->status ? 0 : 1;
            $eventSurvey->update(['status' => $status]);

            $message = 'Status pdated successfully.';
            return $this->view(data: ['message' => $message], flashMessage: $message, component: $route, returnType: 'redirect');

        } catch (\Throwable $th) {
            \Log::error($th);

            $message = 'Could not update status';
            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: $route, returnType: 'redirect');
        }
    }
}
