@component('mail::message', ['headerTitle' => $organiserName, 'preferences' => $preferences])
{!! $content !!}
@endcomponent
