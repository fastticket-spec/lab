<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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

    protected $appends = ['event_image_url', 'event_banner_url'];

    public function organiser(): BelongsTo
    {
        return $this->belongsTo(Organiser::class);
    }

    public function getEventImageUrlAttribute()
    {
        return $this->logo ? Storage::disk(config('filesystems.default'))->url($this->logo) : null;
    }

    public function getEventBannerUrlAttribute()
    {
        return $this->banner ? Storage::disk(config('filesystems.default'))->url($this->banner) : null;
    }
}
