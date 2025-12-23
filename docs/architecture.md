# Arquitetura

## Visão geral
Este projeto é uma aplicação de crowdfunding com:
- **Backend** em Laravel (API + renderização do entrypoint SPA)
- **Frontend** em Vue 3 (SPA via Vite)
- **Persistência** em banco relacional via migrations (foco em PostgreSQL em produção)
- **Pagamentos** plugáveis via um contrato (`PaymentService`) com drivers (`mock` e `mercadopago`)

A SPA é servida por rotas GET do Laravel e consome uma API em `/api` usando **cookies de sessão** e **CSRF** (mesmo domínio).

## Componentes

### 1) Web (SPA entrypoint)
- Rotas GET renderizam `resources/views/spa.blade.php` (view `spa`).
- O Vue Router roda no browser em history mode e usa fallback do Laravel para páginas internas.

### 2) API (session + CSRF)
- As rotas em `routes/api.php` são agrupadas com middleware `web` para habilitar:
  - sessão (cookies)
  - CSRF (para POST/PUT/DELETE)
- Autenticação é baseada em sessão (login/register/logout) e endpoint de “quem sou eu”.

### 3) Pagamentos (drivers)
- A aplicação usa um contrato de pagamentos para isolar integrações.
- Driver ativo é definido por `PAYMENTS_DRIVER` (`config/payments.php`).

Drivers suportados:
- `mock`: simula cartão (pago imediato) e PIX (pendente + “copia e cola”).
- `mercadopago`: implementa criação de pagamento PIX server-side e confirmação via webhook.

### 4) Webhooks (sem CSRF)
- Webhook público do Mercado Pago: `POST /api/webhooks/mercadopago`.
- Essa rota **não** usa `web` middleware (logo, sem CSRF), e é protegida por `throttle`.
- O webhook consulta a API do provedor para obter o status “real” do pagamento antes de alterar a pledge.

## Fluxos principais

### Auth (sessão)
1. SPA chama `POST /api/login` ou `POST /api/register`
2. Laravel cria sessão (cookie)
3. SPA usa `GET /api/me` para obter o usuário atual
4. Logout em `POST /api/logout`

### Navegação SPA
- Laravel atende qualquer GET de tela SPA (ex.: `/`, `/campaigns/:slug`, `/dashboard` etc.) com a view `spa`.
- O Vue Router escolhe o componente de view e o módulo de SEO client-side aplica meta tags.

### Criar campanha e publicar
1. Usuário autenticado cria campanha via endpoints `/api/me/campaigns...`
2. Campanha nasce como `draft` e pode ser publicada (muda para `active`).

### Apoiar campanha (pledge)
1. SPA cria pledge via `POST /api/pledges`
2. Backend cria registro de pledge e chama `PaymentService::processPayment(...)`
3. Backend persiste `provider_payment_id` e `provider_payload` para rastreio
4. Para cartão (mock): confirma imediatamente
5. Para PIX: retorna `next_action` (ex.: copia/cola) e aguarda confirmação:
   - mock: `POST /api/pledges/{id}/confirm`
   - Mercado Pago: confirmação via webhook

## Decisões importantes

### Sessão + CSRF em `/api`
- Opção deliberada para SPA same-origin com segurança padrão do Laravel.
- Implica: o frontend deve enviar cookies (`withCredentials`) e obter/usar CSRF token.

### Webhook fora de `web`
- Necessário para chamadas externas (Mercado Pago) sem CSRF.
- O controlador confirma status consultando a API do MP para reduzir risco de payloads forjados.

### Armazenamento de imagens
- Avatares e capas são servidos via `public/storage` (disco `public`).

## Observação de branding
Há strings fixas de nome em alguns pontos do código (ex.: footer SPA e/ou payloads do mock). Para documentação e configuração, prefira tratar o nome do produto como **APP_NAME/configuração**.
