# Envio de mensagens (e-mails) — Origo

Este documento descreve como o Origo envia mensagens transacionais por e-mail via **Laravel Notifications**, quais eventos disparam cada mensagem, como funciona o **scheduler/jobs** e como testar.

## Visão geral

- O Origo usa **Notifications** (Laravel) para envio de e-mails.
- Os e-mails são renderizados via **Markdown mail** (`MailMessage->markdown(...)`).
- O logo do Origo é servido de `public/img/logo.svg` e usado nos templates.
- Algumas mensagens são disparadas “no fluxo” (controllers/actions/webhook) e outras por **jobs agendados**.

## Onde ficam os arquivos

- Notifications: `app/Notifications/*`
- Templates (Markdown mail):
  - Pledges/pagamentos: `resources/views/emails/pledges/*`
  - Campanhas: `resources/views/emails/campaigns/*`
- Jobs: `app/Jobs/*`
- Scheduler (Laravel 11/12): `bootstrap/app.php` usando `->withSchedule(...)`

## Mensagens implementadas

### Pledges / Pagamentos

1) **Pix gerado (instruções de pagamento)**
- Notification: `App\Notifications\PledgePixGenerated`
- Template: `resources/views/emails/pledges/pix-generated.blade.php`
- Disparo:
  - Quando o usuário cria um pledge com `payment_method=pix` e o provider retorna `next_action`.
  - Controller: `app/Http/Controllers/Api/PledgeController.php`

2) **Pagamento confirmado**
- Notification: `App\Notifications\PledgePaymentConfirmed`
- Template: `resources/views/emails/pledges/payment-confirmed.blade.php`
- Disparo:
  - Quando um pledge transiciona para `paid`.
  - Pode acontecer imediatamente no `mock` (card) ou via webhook no Mercado Pago.

3) **Pagamento falhou/cancelado**
- Notification: `App\Notifications\PledgePaymentFailed`
- Template: `resources/views/emails/pledges/payment-failed.blade.php`
- Disparo:
  - Quando o provider sinaliza `rejected/cancelled` e o pledge é marcado como `canceled`.
  - Webhook: `app/Http/Controllers/Webhooks/MercadoPagoWebhookController.php`

4) **Pagamento reembolsado/chargeback**
- Notification: `App\Notifications\PledgePaymentRefunded`
- Template: `resources/views/emails/pledges/payment-refunded.blade.php`
- Disparo:
  - Quando o provider sinaliza `refunded/charged_back` e o pledge é marcado como `refunded`.
  - Webhook: `app/Http/Controllers/Webhooks/MercadoPagoWebhookController.php`

5) **Checkout incompleto (lembrete)**
- Notification: `App\Notifications\PledgeCheckoutIncomplete`
- Template: `resources/views/emails/pledges/checkout-incomplete.blade.php`
- Disparo:
  - Job agendado encontra pledges Pix `pending` antigos e envia lembrete.
  - Job: `app/Jobs/SendCheckoutIncompleteRemindersJob.php`
- Dedup:
  - Coluna `pledges.checkout_incomplete_reminded_at` evita reenviar.

### Campanhas

6) **Campanha terminando (para seguidores do criador)**
- Notification: `App\Notifications\CampaignEndingSoon`
- Template: `resources/views/emails/campaigns/ending-soon.blade.php`
- Disparo:
  - Job busca campanhas `active` que terminam em até N dias.
  - Job: `app/Jobs/SendCampaignEndingSoonNotificationsJob.php`
- Dedup:
  - Coluna `campaigns.ending_soon_notified_at`.

7) **Meta atingida (para o criador)**
- Notification: `App\Notifications\CampaignGoalReached`
- Template: `resources/views/emails/campaigns/goal-reached.blade.php`
- Disparo:
  - Quando um pagamento confirmado faz a campanha cruzar a meta pela primeira vez.
  - Action: `app/Actions/Pledge/ConfirmPayment.php`
- Dedup:
  - Coluna `campaigns.goal_reached_notified_at`.

8) **Campanha finalizada — bem-sucedida (criador + apoiadores pagos)**
- Notification: `App\Notifications\CampaignSuccessful`
- Template: `resources/views/emails/campaigns/successful.blade.php`
- Disparo:
  - Job finaliza campanhas com `ends_at <= now()` e define status `successful/failed`.
  - Job: `app/Jobs/FinalizeEndedCampaignsJob.php`
- Dedup:
  - Coluna `campaigns.finished_notified_at`.

9) **Campanha finalizada — não atingiu meta (criador + apoiadores pagos)**
- Notification: `App\Notifications\CampaignFailed`
- Template: `resources/views/emails/campaigns/failed.blade.php`
- Disparo/dedup: igual ao item acima.

10) **Nova campanha publicada (para seguidores do criador)**
- Notification: `App\Notifications\NewCampaignPublished`
- Disparo:
  - Na publicação da campanha.
  - Action: `app/Actions/Campaign/PublishCampaign.php`

## Scheduler (tarefas agendadas)

O agendamento está em `bootstrap/app.php` usando `->withSchedule(...)`.

Tarefas atualmente configuradas:
- `SendCheckoutIncompleteRemindersJob` — `everyTenMinutes()`
- `SendCampaignEndingSoonNotificationsJob` — `hourly()`
- `FinalizeEndedCampaignsJob` — `hourly()`

Todos rodam com `withoutOverlapping()`.

Para produção, configure o cron do servidor para rodar:
- `php artisan schedule:run` a cada minuto

## Configurações (env)

1) Checkout incompleto:
- `CHECKOUT_INCOMPLETE_DELAY_MINUTES` (default: `60`)
- Lido de: `config/payments.php` → `payments.checkout_incomplete.delay_minutes`

2) Campanha terminando:
- `CAMPAIGN_ENDING_SOON_DAYS` (default: `3`)
- Lido de: `config/campaigns.php` → `campaigns.ending_soon.days`

## Deduplicação (anti-spam)

Para evitar envios repetidos, usamos timestamps no banco:

- `pledges.checkout_incomplete_reminded_at`
- `campaigns.ending_soon_notified_at`
- `campaigns.finished_notified_at`
- `campaigns.goal_reached_notified_at`

As migrations correspondentes estão em `database/migrations/` (datas recentes).

## Como testar

### Testes automatizados

Rodar todos:
- `php artisan test`

Rodar apenas os novos testes:
- `php artisan test --filter=CheckoutIncompleteReminderTest`
- `php artisan test --filter=CampaignLifecycleNotificationsTest`

### Teste manual (dev)

1) Configure o mailer no `.env` (SMTP/Mailgun/etc.) e `APP_URL`.
2) Rode:
- `php artisan schedule:run` (executa jobs “due”)

Dicas:
- Para disparar o lembrete de checkout incompleto rapidamente:
  - `CHECKOUT_INCOMPLETE_DELAY_MINUTES=1 php artisan schedule:run`

## Como adicionar uma nova mensagem

1) Crie uma Notification em `app/Notifications/`.
2) Crie o template em `resources/views/emails/...`.
3) Decida o ponto de disparo:
   - No fluxo (controller/action/webhook), ou
   - Job agendado (criar em `app/Jobs/` e registrar em `bootstrap/app.php`).
4) Se houver risco de repetição, adicione **dedup**:
   - Preferir coluna `*_notified_at` no modelo relevante.
5) Adicione teste Feature cobrindo:
   - Disparo esperado
   - Não duplicação

## Observações

- Para pagamentos via Mercado Pago, a transição final geralmente vem do webhook.
- Em `mock`, alguns fluxos (ex.: cartão) podem confirmar imediatamente.
