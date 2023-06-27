<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Organiser extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'account_id',
        'name',
        'name_arabic',
        'about',
        'about_arabic',
        'email',
        'phone',
        'facebook',
        'twitter',
        'snapchat',
        'instagram',
        'youtube',
        'organiser_logo',
        'organiser_logo_arabic',
        'banner',
        'banner_arabic',
        'status'
    ];

    protected $appends = ['logo_url', 'logo_arabic_url', 'banner_url', 'banner_arabic_url'];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function getLogoUrlAttribute()
    {
        return $this->organiser_logo ? Storage::disk(config('filesystems.default'))->url($this->organiser_logo) : null;
    }

    public function getLogoArabicUrlAttribute()
    {
        return $this->organiser_logo_arabic ? Storage::disk(config('filesystems.default'))->url($this->organiser_logo_arabic) : null;
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner ? Storage::disk(config('filesystems.default'))->url($this->banner) : null;
    }

    public function getBannerArabicUrlAttribute()
    {
        return $this->banner_arabic ? Storage::disk(config('filesystems.default'))->url($this->banner_arabic) : null;
    }
}
