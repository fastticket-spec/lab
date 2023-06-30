<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendee extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    const STATUS = [
        'PENDING' => 0,
        'APPROVED' => 1,
        'DECLINED' => 2
    ];

    const ACCEPT_STATUS = [
        'NOT_ACCEPTED' => 0,
        'ACCEPTED' => 1
    ];

    protected $fillable = [
        'access_level_id', 'event_id', 'organiser_id', 'ref', 'email', 'answers', 'status', 'accept_status'
    ];
}
