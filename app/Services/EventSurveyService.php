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

    public function createSurvey(Request $request, string $eventId)
    {
        DB::beginTransaction();

        $this->updateOrCreate(['event_id' => $eventId],
            ['access_levels' => $request->access_levels]
        );

        $eventSurvey = $this->findOneBy(['event_id' => $eventId]);

        if ($eventSurvey && $eventSurvey->surveys) {
            $eventSurvey->surveys()->delete();
        }

        foreach ($request->surveys as $survey) {
            $eventSurvey->surveys()->create([
                'event_id' => $eventId,
                'event_survey_id' => $eventSurvey->id,
                'title' => $survey['title'],
                'title_arabic' => $survey['title_arabic'],
                'type' => $survey['type'],
                'options' => $survey['options'],
                'required' => $survey['required'],
            ]);
        }
        DB::commit();

        $message = 'Survey updated successfully';

        return $this->view(data: ['message' => $message], flashMessage: $message, component: "/event/$eventId/surveys", returnType: 'redirect');
    }
}
