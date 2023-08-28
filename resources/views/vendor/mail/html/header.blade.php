@props(['url', 'image'])
<tr>
<td class="header" style="padding-bottom: 0 !important">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
@if($image)
<img src="{{$image}}" alt="header image" style="width: 570px">
@endif
{{ $slot }}
@endif
</a>
</td>
</tr>
