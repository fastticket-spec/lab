@component('mail::message', ['headerTitle' => $organiserName, 'headerLogo' => $organiserLogo])
### Dear {{$firstName}},

{!! $content !!}
@endcomponent
