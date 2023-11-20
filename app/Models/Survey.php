<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id', 'event_id', 'title', 'title_arabic', 'type', 'options', 'required', 'private', 'parent_index', 'parent_answer', 'input_type', 'input_length'
    ];

    protected $casts = [
        'options' => 'array'
    ];
}
