<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendeeScan extends Model
{
    use HasFactory;

    protected $fillable = ['attendee_id', 'scan', 'scan_user_id'];
}
