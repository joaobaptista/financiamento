@php
    /** @var string $recipientName */
    /** @var string $campaignTitle */
    /** @var string $campaignUrl */
    /** @var string $amount */
@endphp

@component('mail::message')
@if(!empty($recipientName))
Olá, {{ $recipientName }}!
@else
Olá!
@endif

# Reembolso processado

Seu reembolso referente ao apoio de **{{ $amount }}** para a campanha **{{ $campaignTitle }}** foi processado.

@component('mail::button', ['url' => $campaignUrl])
Ver campanha
@endcomponent
@endcomponent
