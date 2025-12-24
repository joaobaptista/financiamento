@php
    /** @var string $logoUrl */
    /** @var string $campaignTitle */
    /** @var string $campaignUrl */
@endphp

@component('mail::message')
<div style="text-align: center; margin: 6px 0 24px;">
    <img src="{{ $logoUrl }}" alt="Origo" width="140" style="display: inline-block; height: auto; max-width: 100%;" />
</div>

# Campanha financiada!

A campanha **{{ $campaignTitle }}** atingiu a meta e foi financiada.

@component('mail::button', ['url' => $campaignUrl])
Ver campanha
@endcomponent

Obrigado por fazer parte disso,
**Equipe do Origo**
@endcomponent
