<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLevelPageDesign extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'access_level_id',
        'btn_color_code',
        'btn_font_color_code',
        'register_btn_value',
        'register_btn_value_ar',
        'form_btn_value',
        'form_btn_value_ar',
        'bg_color',
        'bg_type',
        'bg_image',
        'logo',
        'form_bg_color',
        'field_color',
        'font_color'
    ];
}
