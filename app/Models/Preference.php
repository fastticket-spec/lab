<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'organiser_id',
        'email_bg_color',
        'email_font_color',
        'email_qr_color',
        'email_logo_url',
        'email_logo_width',
        'email_logo_height',
        'email_header_image_url',
        'email_footer_image_url'
    ];
}
