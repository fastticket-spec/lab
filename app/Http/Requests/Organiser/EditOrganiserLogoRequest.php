<?php

namespace App\Http\Requests\Organiser;

use Illuminate\Foundation\Http\FormRequest;

class EditOrganiserLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organiser_logo' => 'mimes:jpeg,jpg,png|max:3000|nullable',
            'organiser_logo_arabic' => 'mimes:jpeg,jpg,png|max:3000|nullable',
            'organiser_banner' => 'mimes:jpeg,jpg,png|max:4000|nullable',
            'organiser_banner_arabic' => 'mimes:jpeg,jpg,png|max:4000|nullable',
        ];
    }
}
