@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">
<img src="{{ asset('logo/logo.jpg') }}" class="logo" alt="{{ config('app.name') }} Logo" style="height: 40px; width: auto; margin: 0 10px 0 0;">
<span style="font-size: 20px; font-weight: 700; color: #1b3c48;">{{ config('app.name') }}</span>
</a>
</td>
</tr>
