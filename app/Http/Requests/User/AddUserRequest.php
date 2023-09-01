<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class AddUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'email' => 'email|required',
            'phone' => 'string',
            'ext' => 'required_with:phone',
            'role_id' => 'required|exists:roles,id',
            'event_ids' => 'required|array',
            'event_ids.*' => 'required|exists:events,id',
            'all_events' => 'boolean|required'
        ];
    }
}
