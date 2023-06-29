<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AccessLevel extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'event_id', 'title', 'title_arabic', 'quantity_available', 'quantity_filled', 'status'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function surveyAccessLevels(): HasOne
    {
        return $this->hasOne(EventSurveyAccessLevel::class);
    }

    public function generalSettings(): HasOne
    {
        return $this->hasOne(AccessLevelGeneralSetting::class);
    }

    public function pageDesign(): HasOne
    {
        return $this->hasOne(AccessLevelPageDesign::class);
    }
}
