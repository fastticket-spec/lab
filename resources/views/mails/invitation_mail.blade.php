@component('mail::message', ['headerTitle' => $organiserName, 'headerLogo' => $organiserLogo])
{!! $content !!}
@endcomponent
