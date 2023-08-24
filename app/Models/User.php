<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasUuids;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'parent_account_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function account(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Account::class, 'owner', 'id');
    }

    public function userRole()
    {
        $account = $this->account;
        return $account ? optional($account->role)->role : '';
    }

    public function organiserIds()
    {
        $account = $this->parentAccount ?: $this->account;

        return $account ? optional($account->organiser)->pluck('id') : [];
    }

    public function parentAccount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_account_id', 'id');
    }

    public function activeOrganiser()
    {
        return optional($this->account)->active_organiser;
    }

    public function userEventAccess()
    {
        return $this->account->eventAccess()->get();
    }

    public function userEventAccessId(): Collection|null
    {
        if ($this->userRole()) {
            $eventIds = $this->account->eventAccess()->get()->map(fn($eventAccess) => $eventAccess->event_id);

            return $eventIds->count() > 0 ? $eventIds : null;
        }

        return null;
    }
}
