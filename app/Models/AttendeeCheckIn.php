<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendeeCheckIn extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'attendee_id', 'checkin', 'checkin_user_id', 'checkout'
    ];

    public function attendee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Attendee::class);
    }

    public function checkinUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'checkin_user_id');
    }
}
