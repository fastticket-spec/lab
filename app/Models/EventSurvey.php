<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSurvey extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['event_id', 'access_levels'];

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }

    protected $casts = [
        'access_levels' => 'array'
    ];
}
