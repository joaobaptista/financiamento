@php
    /** @var string $recipientName */
    /** @var string $campaignTitle */
    /** @var string $campaignUrl */
    /** @var \Illuminate\Support\Carbon|null $endsAt */
@endphp

@component('mail::message')
@if(!empty($recipientName))
Olá, {{ $recipientName }}!
@else
Olá!
@endif

# Últimos dias!

A campanha **{{ $campaignTitle }}** está chegando ao fim.

Se você ainda quer apoiar, este é um ótimo momento para fazer sua contribuição.

@component('mail::button', ['url' => $campaignUrl])
Ver campanha
@endcomponent
@endcomponent
