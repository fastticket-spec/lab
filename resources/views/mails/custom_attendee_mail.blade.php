@component('mail::message', ['headerTitle' => $organiserName, 'headerLogo' => $organiserLogo])
    {{ $data['content'] }}
@endcomponent
