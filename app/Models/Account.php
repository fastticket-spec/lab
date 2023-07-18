<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function organiser(): HasMany
    {
        return $this->hasMany(Organiser::class, 'account_id', 'id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner', 'id');
    }

    public function eventAccess(): HasMany
    {
        return $this->hasMany(AccountEventAccess::class, 'account_id', 'id');
    }
}
