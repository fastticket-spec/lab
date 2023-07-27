<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory, HasUuids;

    const STATUS = [
        'ACTIVE' => 1,
        'INACTIVE' => 0
    ];

    const STATUS_READABLE = [
        self::STATUS['ACTIVE'] => 'active',
        self::STATUS['INACTIVE'] => 'inactive',
    ];

    protected $fillable = [
        'event_id', 'area'
    ];

    public function event(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
