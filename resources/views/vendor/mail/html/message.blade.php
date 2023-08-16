<x-mail::layout bodyBg="{{$preferences['email_bg_color']}}" fontColor="{{$preferences['email_font_color']}}">
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
    @if($preferences['email_logo_url'])
    <img src="{{$preferences['email_logo_url']}}" style="width: 200px; height: auto;" alt="">
    @else
    {{$headerTitle ?? config('app.name')}}
    @endif
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ $headerTitle ?? config('app.name') }}. @lang('All rights reserved.')
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
