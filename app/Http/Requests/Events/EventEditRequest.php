<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;

class EventEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'string',
            'title_arabic' => 'string|nullable',
            'description' => 'string',
            'description_arabic' => 'string|nullable',
            'event_image_upload' => 'nullable|mimes:jpeg,jpg,png|max:3000',
            'event_banner_upload' => 'nullable|mimes:jpeg,jpg,png|max:4000',
        ];
    }
}
