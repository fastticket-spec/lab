<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendeeUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'access_level_id' => 'required',
            'attendees' => 'array|required',
            'attendees.*.first_name' => 'required',
            'attendees.*.last_name' => 'required',
            'attendees.*.email' => 'required|email'
        ];
    }
}
