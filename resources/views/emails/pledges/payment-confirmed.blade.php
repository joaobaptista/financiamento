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

# Apoio confirmado

Confirmamos seu apoio de **{{ $amount }}** para a campanha **{{ $campaignTitle }}**.

@component('mail::button', ['url' => $campaignUrl])
Ver campanha
@endcomponent

Obrigado por apoiar criadores na plataforma.
@endcomponent
