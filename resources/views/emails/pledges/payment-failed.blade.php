@php
    /** @var string $logoUrl */
    /** @var string $campaignTitle */
    /** @var string $campaignUrl */
    /** @var string $amount */
    /** @var string|null $reason */
@endphp

@component('mail::message')
<div style="text-align: center; margin: 6px 0 24px;">
    <img src="{{ $logoUrl }}" alt="Origo" width="140" style="display: inline-block; height: auto; max-width: 100%;" />
</div>

# Não conseguimos confirmar seu pagamento

Seu apoio de **{{ $amount }}** para a campanha **{{ $campaignTitle }}** não foi confirmado.

@if(!empty($reason))
Motivo: {{ $reason }}
@endif

Se você ainda quiser apoiar, tente novamente gerando um novo pagamento.

@component('mail::button', ['url' => $campaignUrl])
Ver campanha
@endcomponent

Abraços,
**Equipe do Origo**
@endcomponent
