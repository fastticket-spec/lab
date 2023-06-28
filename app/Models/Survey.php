<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id', 'event_id', 'title', 'title_arabic', 'type', 'options', 'required'
    ];

    protected $casts = [
        'options' => 'array'
    ];
}
