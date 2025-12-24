@php
    /** @var string $logoUrl */
    /** @var string $campaignTitle */
    /** @var string $campaignUrl */
    /** @var string $amount */
    /** @var string $copyPaste */
    /** @var string|null $expiresAt */
@endphp

@component('mail::message')
<div style="text-align: center; margin: 6px 0 24px;">
    <img src="{{ $logoUrl }}" alt="Origo" width="140" style="display: inline-block; height: auto; max-width: 100%;" />
</div>

# Seu Pix foi gerado

Seu Pix para apoiar **{{ $campaignTitle }}** no valor de **{{ $amount }}** já está pronto.

@if($copyPaste)
@component('mail::panel')
**Copia e cola (Pix):**

{{ $copyPaste }}
@endcomponent
@endif

@if($expiresAt)
Válido até: {{ $expiresAt }}
@endif

@component('mail::button', ['url' => $campaignUrl])
Ver campanha
@endcomponent

Abraços,
**Equipe do Origo**
@endcomponent
