<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLevelRequestForm extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'access_level_id',
        'message_before',
        'message_before_arabic',
        'message_after',
        'message_after_arabic',
    ];
}
