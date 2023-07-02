<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventBadge extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'event_id', 'badge_id', 'fileName', 'startTemplateUrl', 'html', 'status'
    ];
}
