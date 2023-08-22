<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormEmail extends Model
{
    use HasFactory;

    protected $fillable = ['organiser_id', 'event_id', 'access_level_id', 'email', 'severity'];
}
