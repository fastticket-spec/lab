<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccessLevelGeneralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "visibility" => 'string|required',
            "accept_reject" => 'string|required',
            "waiting_list" => 'string|required',
            "send_tc" => 'string|required',
            "title" => 'string|required',
            "title_arabic" => 'string|nullable',
            "quantity_available" => 'integer|nullable',
            "description" => 'string|required',
            "description_arabic" => 'string|nullable',
            "success_message" => 'string|required',
            "success_message_arabic" => 'string|nullable',
            "approval_message_title" => 'string|required',
            "approval_message" => 'string|required',
            "email_message_title" => 'string|required',
            "email_message" => 'string|required',
            "email_message_arabic" => 'string|nullable',
            "start_on" => 'string|nullable',
            "end_on" => 'string|nullable',
        ];
    }
}
