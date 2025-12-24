@php
    /** @var string $logoUrl */
    /** @var string $campaignTitle */
    /** @var string $campaignUrl */
    /** @var string $amount */
@endphp

@component('mail::message')
<div style="text-align: center; margin: 6px 0 24px;">
    <img src="{{ $logoUrl }}" alt="Origo" width="140" style="display: inline-block; height: auto; max-width: 100%;" />
</div>

# Apoio confirmado

Confirmamos seu apoio de **{{ $amount }}** para a campanha **{{ $campaignTitle }}**.

@component('mail::button', ['url' => $campaignUrl])
Ver campanha
@endcomponent

Obrigado por apoiar criadores na plataforma.

Abraços,
**Equipe do Origo**
@endcomponent
