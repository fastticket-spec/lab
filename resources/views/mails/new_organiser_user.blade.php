@component('mail::message', ['headerTitle' => $organiserName, 'headerLogo' => $organiserLogo])
### Hello {{$name}},

You've been invited to Easy Accredite Dashboard as {{$role}}. Your login details are as follows:

Email: {{$email}}<br>
Password: {{$password}}

Kindly change your password once you've logged in.

Welcome on-board.

<x-mail::button url="{{config('app.url')}}">
    Visit dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
