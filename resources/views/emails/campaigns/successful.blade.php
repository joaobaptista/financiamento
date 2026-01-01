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

# Campanha financiada!

A campanha **{{ $campaignTitle }}** atingiu a meta e foi financiada.

@component('mail::button', ['url' => $campaignUrl])
Ver campanha
@endcomponent
@endcomponent
