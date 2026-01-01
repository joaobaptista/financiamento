@php
    /** @var string $recipientName */
    /** @var string $projectTitle */
    /** @var string $projectUrl */
    /** @var string|null $supportCenterUrl */
    /** @var string|null $supportUrl */
@endphp

@component('mail::message')
@if(!empty($recipientName))
Olá, {{ $recipientName }}!
@else
Olá!
@endif

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
@endcomponent
