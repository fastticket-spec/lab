<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    const EVENT_STATUS = [
        'INACTIVE' => 0,
        'ACTIVE' => 1
    ];

    const EVENT_STATUS_READABLE = [
        self::EVENT_STATUS['INACTIVE'] => 'inactive',
        self::EVENT_STATUS['ACTIVE'] => 'active'
    ];

    protected $fillable = [
        'organiser_id', 'title', 'title_arabic', 'description', 'description_arabic', 'logo', 'banner', 'status'
    ];

    public function organiser(): BelongsTo
    {
        return $this->belongsTo(Organiser::class);
    }
}
