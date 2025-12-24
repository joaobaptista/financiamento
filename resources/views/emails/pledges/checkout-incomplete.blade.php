@php
    /** @var string $logoUrl */
    /** @var string $projectTitle */
    /** @var string $projectUrl */
    /** @var string|null $supportCenterUrl */
    /** @var string|null $supportUrl */
@endphp

@component('mail::message')
<div style="text-align: center; margin: 6px 0 24px;">
    <img src="{{ $logoUrl }}" alt="Origo" width="140" style="display: inline-block; height: auto; max-width: 100%;" />
</div>

# Olá!

Verificamos que você iniciou um apoio para o projeto **{{ $projectTitle }}**, mas não chegou a concluí-lo.

Para finalizar sua contribuição, clique no botão abaixo e inicie um novo apoio:

@component('mail::button', ['url' => $projectUrl])
Apoiar este projeto
@endcomponent

@if(!empty($supportCenterUrl) || !empty($supportUrl))
Se ficar com alguma dúvida,
@if(!empty($supportCenterUrl))
você pode conferir nossa Central de Suporte: {{ $supportCenterUrl }}
@endif
@if(!empty($supportUrl))
ou falar com nossa equipe: {{ $supportUrl }}
@endif
@endif

Abraços,
**Equipe do Origo**
@endcomponent
