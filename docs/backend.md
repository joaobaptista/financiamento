# Backend (Laravel)

## Stack
- Laravel 12
- API em `/api` (session-based)
- Migrations para schema
- Integrações via Services/Contracts

## Organização de pastas (alto nível)
- `app/Http/Controllers/Api`: endpoints consumidos pela SPA
- `app/Http/Controllers/Webhooks`: endpoints públicos para provedores externos
- `app/Actions`: regras de negócio orquestradas (ex.: criar pledge, confirmar pagamento)
- `app/Domain`: modelos Eloquent de domínio (ex.: Campaign, Pledge)
- `app/Services`: integrações (pagamentos, dinheiro, imagens)
- `app/Contracts`: contratos de serviços (ex.: payments)
- `app/Enums`: enums de status

## Rotas

### SPA entrypoint (GET)
Definidas em `routes/web.php`:
- GET `/`, `/campaigns`, `/campaigns/{slug}` → view `spa`
- Rotas autenticadas (dashboard, profile, criação/edição de campanha) também servem `spa`
- Fallback: GET `/{any}` → view `spa`

### API (session + CSRF)
Definidas em `routes/api.php` sob middleware `web`:

**Auth**
- GET `/api/me`
- POST `/api/login` (guest + throttle)
- POST `/api/register` (guest + throttle)
- POST `/api/logout` (auth)

**Campanhas públicas**
- GET `/api/campaigns`
- GET `/api/campaigns/{slug}`

**Perfil do usuário**
- GET `/api/me/profile`
- POST `/api/me/profile`
- POST `/api/me/password`

**Conferência do apoiador**
- GET `/api/me/supporter-profile`
- POST `/api/me/supporter-profile`

**CEP (ViaCEP)**
- GET `/api/cep/{cep}` (throttle)

**Criador / dashboard**
- GET `/api/me/creator-profile`
- POST `/api/me/creator-profile`
- GET `/api/dashboard/campaigns`
- GET `/api/dashboard/campaigns/{id}`

**CRUD campanhas (autenticado)**
- GET `/api/me/campaigns/{id}`
- POST `/api/me/campaigns`
- PUT `/api/me/campaigns/{id}`
- DELETE `/api/me/campaigns/{id}`
- POST `/api/me/campaigns/{id}/publish`

**Pledges / pagamentos**
- POST `/api/pledges` (throttle)
- GET `/api/pledges/{id}` (throttle)
- POST `/api/pledges/{id}/confirm` (throttle)

### Webhooks (sem `web`)
- POST `/api/webhooks/mercadopago` (throttle)

## Swagger / OpenAPI

O projeto expõe uma UI do Swagger para inspecionar os endpoints e seus payloads.

- UI: GET `/api/documentation`
- JSON gerado: `storage/api-docs/api-docs.json`

Gerar/atualizar a documentação:
- `php artisan l5-swagger:generate`

Notas:
- As anotações OpenAPI ficam em `app/OpenApi`.
- Em produção, prefira gerar o JSON no deploy (ou habilitar `L5_SWAGGER_GENERATE_ALWAYS=true` apenas em dev).

## Pagamentos

## Mensagens (e-mails)

- Guia de envio de mensagens transacionais (Notifications + jobs + scheduler): `docs/messaging.md`

### Contrato
- `app/Contracts/Payments/PaymentService.php`

### Seleção de driver
- `config/payments.php`: `PAYMENTS_DRIVER=mock|mercadopago`

### Driver `mock`
- Simula:
  - `card`: retorna status `paid` imediatamente
  - `pix`: retorna status `pending` e um `next_action` com payload (copia/cola)
- Confirmação do PIX mock acontece via `POST /api/pledges/{id}/confirm`.

### Driver `mercadopago`
- Config em `config/mercadopago.php`:
  - `MERCADOPAGO_ACCESS_TOKEN`
  - `MERCADOPAGO_BASE_URL` (default `https://api.mercadopago.com`)
  - `MERCADOPAGO_WEBHOOK_URL` (default `APP_URL/api/webhooks/mercadopago`)
- PIX é criado server-side; status final é sincronizado por webhook.

### Webhook do Mercado Pago
Controlador: `app/Http/Controllers/Webhooks/MercadoPagoWebhookController.php`
- Extrai `paymentId` do payload
- Localiza a pledge por `provider=mercadopago` e `provider_payment_id`
- Consulta `GET /v1/payments/{id}` no MP
- Atualiza pledge:
  - `approved` → confirma (`ConfirmPayment`)
  - `cancelled|rejected` → cancela
  - `refunded|charged_back` → reembolsa
  - `pending|in_process` → mantém pendente

## Modelos e enums

### Campaign
- `app/Domain/Campaign/Campaign.php`
- Status via `app/Enums/CampaignStatus.php` (`draft|active|successful|failed`)

### Pledge
- `app/Domain/Pledge/Pledge.php`
- Status via `app/Enums/PledgeStatus.php` (`pending|paid|refunded|canceled`)
- Campos relevantes para pagamentos:
  - `payment_method`
  - `provider`
  - `provider_payment_id`
  - `provider_payload`
  - `paid_at`

### User
- `app/Models/User.php`
- Campos de contato/endereço:
  - `postal_code`, `address_*`, `phone`
- Avatar:
  - `profile_photo_path`
  - atributo computado `profile_photo_url` (disco `public`)

## Considerações de segurança
- APIs usam sessão + CSRF (grupo `web`) — apropriado para SPA same-origin.
- Webhook do MP não usa CSRF; proteção atual:
  - rate limit (`throttle`)
  - validação indireta consultando a API do MP antes de confirmar
- Se quiser endurecer: implementar validação de assinatura com `MERCADOPAGO_WEBHOOK_SECRET` (config já existe).
