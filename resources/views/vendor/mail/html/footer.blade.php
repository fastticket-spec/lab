@props(['image'])
<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
@if($image)
    <img src="{{$image}}" alt="footer image" style="width: 570px">
@endif
</td>
</tr>
<tr>
<td class="content-cell" align="center">
{{ Illuminate\Mail\Markdown::parse($slot) }}
</td>
</tr>
</table>
</td>
</tr>
