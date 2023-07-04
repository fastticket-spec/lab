<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLevelSocial extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'access_level_id',
        'email',
        'instagram',
        'twitter',
        'linkedin',
        'youtube',
        'phone_number',
    ];
}
