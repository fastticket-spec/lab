<?php

namespace App\Http\Requests\Organiser;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganiserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('organisers')->where('account_id', auth()->user()->account->id)
                ->ignore($this->id)
            ],
            'email' => 'required|email|unique:organisers,email,' . $this->id,
            'organiser_logo' => 'mimes:jpeg,jpg,png|max:3000',
            'organiser_logo_arabic' => 'nullable|mimes:jpeg,jpg,png|max:3000',
            'organiser_banner' => 'nullable|mimes:jpeg,jpg,png|max:4000',
            'organiser_banner_arabic' => 'nullable|mimes:jpeg,jpg,png|max:4000',
            'name_arabic' => 'string|nullable',
            'about' => 'string|nullable',
            'about_arabic' => 'string|nullable',
            'phone' => 'string|nullable',
            'facebook' => 'string|nullable',
            'twitter' => 'string|nullable',
            'snapchat' => 'string|nullable',
            'instagram' => 'string|nullable',
            'youtube' => 'string|nullable',
            'status' => 'boolean'
        ];
    }
}
