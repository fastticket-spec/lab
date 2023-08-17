@component('mail::message', ['headerTitle' => $organiserName, 'preferences' => $preferences])
### Hello {{$name}},

You've been invited to Easy Accredite Dashboard as an Account Manager. Your login details are as follows:

Email: {{$email}}<br>
Password: {{$password}}

Welcome on-board.

<x-mail::button url="{{config('app.url')}}">
    Visit dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
