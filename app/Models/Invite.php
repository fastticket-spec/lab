<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['event_id', 'access_level_id', 'ref', 'first_name', 'last_name', 'email', 'phone', 'attachment'];
}
