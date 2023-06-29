<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSurvey extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }

    public function surveyAccessLevels(): HasMany
    {
        return $this->hasMany(EventSurveyAccessLevel::class);
    }
}
