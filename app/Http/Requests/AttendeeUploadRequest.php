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
            'attendees.*.First Name' => 'required',
            'attendees.*.Last Name' => 'required',
            'attendees.*.Email Address' => 'required|email'
        ];
    }
}
