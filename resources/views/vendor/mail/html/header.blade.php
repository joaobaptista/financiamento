@props(['url'])

@php
    $baseUrl = rtrim((string) config('app.url'), '/');
    $logoUrl = $baseUrl . '/img/logo.svg';
@endphp

<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<img src="{{ $logoUrl }}" alt="Origo" width="140" style="display: inline-block; height: auto; max-width: 100%;" />
</a>
</td>
</tr>
