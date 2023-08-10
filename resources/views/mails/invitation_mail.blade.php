@component('mail::message', ['headerTitle' => $organiserName, 'headerLogo' => $organiserLogo])
<h3 style="text-align: {{$isArabic ? 'right' : 'left'}}">Dear {{$firstName}},</h3>

{!! $content !!}
@endcomponent
