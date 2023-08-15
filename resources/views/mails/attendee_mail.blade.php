@component('mail::message', ['headerTitle' => $organiserName, 'preferences' => $preferences])
### Dear {{$firstName}},

{!! $content !!}
@endcomponent
