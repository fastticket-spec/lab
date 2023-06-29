<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSurveyAccessLevel extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'event_survey_id', 'access_level_id'
    ];

    public function surveys()
    {
        return $this->hasMany(Survey::class, 'event_survey_id', 'event_survey_id');
    }
}
