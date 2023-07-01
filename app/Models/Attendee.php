<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendee extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    const STATUS = [
        'PENDING' => 0,
        'APPROVED' => 1,
        'DECLINED' => 2
    ];

    const STATUS_READABLE = [
        0 => 'pending',
        1 => 'approved',
        2 => 'declined'
    ];

    const ACCEPT_STATUS = [
        'NOT_ACCEPTED' => 0,
        'ACCEPTED' => 1
    ];

    const ACCEPT_STATUS_READABLE = [
        0 => 'not accepted',
        1 => 'accepted'
    ];

    protected $casts = [
        'answers' => 'array'
    ];

    protected $fillable = [
        'access_level_id', 'event_id', 'organiser_id', 'ref', 'email', 'answers', 'status', 'accept_status'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function accessLevel(): BelongsTo
    {
        return $this->belongsTo(AccessLevel::class);
    }
}
