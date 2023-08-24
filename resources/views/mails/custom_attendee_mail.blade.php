@component('mail::message', ['headerTitle' => $organiserName, 'preferences' => $preferences])
    {{ $data['content'] }}
@endcomponent
