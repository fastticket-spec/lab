<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLevelGeneralSetting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'access_level_id',
        "visibility",
        "accept_reject",
        "waiting_list",
        "send_tc",
        "description",
        "description_arabic",
        "success_message",
        "success_message_arabic",
        "approval_message_title",
        "approval_message",
        "email_message_title",
        "email_message",
        "email_message_arabic",
        "invitation_title",
        "invitation_message",
        "start_on",
        "end_on",
    ];
}
