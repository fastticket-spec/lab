<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                Rule::unique('events')->where('organiser_id', auth()->user()->account->active_organiser)
            ],
            'title_arabic' => 'string|nullable',
            'description' => 'required|string',
            'description_arabic' => 'string|nullable',
            'event_image_upload' => 'mimes:jpeg,jpg,png|max:3000|nullable',
            'event_banner_upload' => 'mimes:jpeg,jpg,png|max:4000|nullable'
        ];
    }
}
