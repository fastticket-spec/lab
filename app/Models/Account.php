<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'timezone_id',
        'currency_id',
        'role_id',
        'active_organiser',
        'access_all_events'
    ];
}
