@php
    /** @var string $logoUrl */
    /** @var string $campaignTitle */
    /** @var string $campaignUrl */
    /** @var \Illuminate\Support\Carbon|null $endsAt */
@endphp

@component('mail::message')
<div style="text-align: center; margin: 6px 0 24px;">
    <img src="{{ $logoUrl }}" alt="Origo" width="140" style="display: inline-block; height: auto; max-width: 100%;" />
</div>

# Últimos dias!

A campanha **{{ $campaignTitle }}** está chegando ao fim.

Se você ainda quer apoiar, este é um ótimo momento para fazer sua contribuição.

@component('mail::button', ['url' => $campaignUrl])
Ver campanha
@endcomponent

Abraços,
**Equipe do Origo**
@endcomponent
