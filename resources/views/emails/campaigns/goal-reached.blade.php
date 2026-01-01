@php
    /** @var string $recipientName */
    /** @var string $campaignTitle */
    /** @var string $campaignUrl */
@endphp

@component('mail::message')
@if(!empty($recipientName))
Olá, {{ $recipientName }}!
@else
Olá!
@endif

# Meta atingida!

A campanha **{{ $campaignTitle }}** acabou de atingir a meta.

@component('mail::button', ['url' => $campaignUrl])
Ver campanha
@endcomponent
@endcomponent
