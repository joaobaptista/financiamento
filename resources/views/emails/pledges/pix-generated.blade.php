@php
    /** @var string $recipientName */
    /** @var string $campaignTitle */
    /** @var string $campaignUrl */
    /** @var string $amount */
    /** @var string $copyPaste */
    /** @var string|null $expiresAt */
@endphp

@component('mail::message')
@if(!empty($recipientName))
Olá, {{ $recipientName }}!
@else
Olá!
@endif

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
@endcomponent
