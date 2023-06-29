<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EventDesignImage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'design_image',
        'event_id',
        'organiser_id'
    ];

    protected $appends = ['design_image_url'];

    public function getDesignImageUrlAttribute()
    {
        return $this->design_image ? Storage::disk(config('filesystems.default'))->url($this->design_image) : null;
    }
}
