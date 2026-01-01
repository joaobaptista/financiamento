@php
    /** @var string $recipientName */
    /** @var string $campaignTitle */
    /** @var string $campaignUrl */
    /** @var string $amount */
    /** @var string|null $reason */
@endphp
@component('mail::message')
@if(!empty($recipientName))
Olá, {{ $recipientName }}!
@else
Olá!
@endif

# Não conseguimos confirmar seu pagamento
@if(!empty($reason))
Motivo: {{ $reason }}
@endif

Se você ainda quiser apoiar, tente novamente gerando um novo pagamento.

@component('mail::button', ['url' => $campaignUrl])
Ver campanha
@endcomponent
@endcomponent
