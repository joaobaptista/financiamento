# Mercado Pago (PIX) — Guia de Integração

Este projeto tem um driver de pagamentos que permite alternar entre `mock` (MVP) e `mercadopago`.

- Backend (Laravel): cria o pagamento PIX no Mercado Pago via API server-side.
- Frontend (SPA): exibe o "copia e cola" e gera QR Code (já existe gerador no frontend).
- Webhook: Mercado Pago notifica mudanças de status; o backend confirma/cancela/reembolsa o pledge.

## 1) Visão geral da arquitetura

### Driver / Service
- Contrato: `App\Contracts\Payments\PaymentService`
- Drivers:
  - `App\Services\Payments\MockPaymentService`
  - `App\Services\Payments\MercadoPagoPaymentService`
- Seleção do driver:
  - `PAYMENTS_DRIVER=mock|mercadopago`
  - Config: `config/payments.php`

### Fluxo PIX (alto nível)
1. Usuário cria um pledge em `POST /api/pledges` com `payment_method=pix`
2. Backend cria o registro de `pledge` e chama Mercado Pago `POST /v1/payments`
3. Backend retorna para o frontend:
   - `payment.next_action.copy_paste` (texto do PIX copia e cola)
   - opcional: `payment.next_action.qr_code_base64`
4. Usuário paga no app do banco
5. Mercado Pago chama o webhook do projeto
6. Backend consulta o pagamento na API do MP e atualiza o `pledge`

## 2) Variáveis de ambiente

No `.env`:

```env
# Seleciona driver
PAYMENTS_DRIVER=mercadopago

# Mercado Pago (server-side)
MERCADOPAGO_ACCESS_TOKEN=SEU_ACCESS_TOKEN

# Mercado Pago (frontend)
# Public Key pode ficar no frontend (é pública), mas ainda assim evite commitar valores reais.
VITE_MERCADOPAGO_PUBLIC_KEY=SUA_PUBLIC_KEY

# Opcional
MERCADOPAGO_BASE_URL=https://api.mercadopago.com
MERCADOPAGO_CURRENCY=BRL

# URL que o Mercado Pago chamará (pode ser montada automaticamente via APP_URL)
# Se vazio, o default será: {APP_URL}/api/webhooks/mercadopago
MERCADOPAGO_WEBHOOK_URL=

# Reservado para validação de webhook (ainda não obrigatório no código atual)
MERCADOPAGO_WEBHOOK_SECRET=
```

Observação: **nunca** exponha `MERCADOPAGO_ACCESS_TOKEN` no frontend.

## 3) Endpoint de webhook

- URL: `POST /api/webhooks/mercadopago`
- Motivo: webhook não usa sessão/CSRF (precisa ser acessível pelo Mercado Pago).
- Segurança atual (MVP):
  - O controller **faz fetch** do pagamento no MP usando `MERCADOPAGO_ACCESS_TOKEN` antes de confirmar.
  - Existe `throttle` na rota.

### Configurar no painel do Mercado Pago
- Cadastre o webhook apontando para:
  - `https://SEU_DOMINIO/api/webhooks/mercadopago`
- Recomendado: usar HTTPS.

## 4) PIX — criação do pagamento

O driver Mercado Pago cria pagamentos via:
- `POST https://api.mercadopago.com/v1/payments`
- Headers:
  - `Authorization: Bearer {MERCADOPAGO_ACCESS_TOKEN}`
  - `X-Idempotency-Key: pledge_{id}` (para evitar duplicação)

Campos usados (resumo):
- `transaction_amount` (em reais, com 2 casas)
- `description`
- `payment_method_id=pix`
- `payer.email`
- `external_reference` (ex.: `pledge_123`)
- `notification_url` (se configurada)

### Retorno exibido no frontend
O backend tenta ler:
- `point_of_interaction.transaction_data.qr_code` (copia e cola)
- `point_of_interaction.transaction_data.qr_code_base64` (imagem do QR)

Se vier apenas `qr_code` (texto), a SPA já gera o QR Code localmente.

## 5) Status e sincronização

Mapeamento atual (backend → `PledgeStatus`):
- `approved` → `paid`
- `cancelled`/`rejected` → `canceled`
- `refunded`/`charged_back` → `refunded`
- outros → `pending`

O webhook atualiza `provider_payload` com o JSON completo da API do MP (auditoria/debug).

## 6) Cartão (próxima etapa)

O driver Mercado Pago **ainda não processa cartão**.

Para cartão em produção é necessário:
- Tokenização no frontend via SDK do Mercado Pago
- Enviar ao backend um `token` (não dados do cartão)
- Backend cria o pagamento com o token e captura status

Se você quiser, a próxima entrega pode ser:
- integrar SDK no SPA para gerar token
- ajustar `StorePledgeRequest`/payload
- expandir `MercadoPagoPaymentService` para `card`.

### 6.1) Cartão (SDK) — fluxo recomendado

Objetivo: **nunca** enviar número do cartão/CVV para o backend. O backend recebe apenas um *token* e cria o pagamento no Mercado Pago.

**Passos (alto nível)**
1. Frontend coleta dados do cartão (apenas para tokenização)
2. Frontend chama o SDK do Mercado Pago e gera `card_token`
3. Frontend chama `POST /api/pledges` com `payment_method=card` + `card_token` + campos mínimos do pagador
4. Backend cria o pagamento na API do Mercado Pago e retorna status
5. Webhook continua sendo a fonte de verdade para mudanças assíncronas (ex.: `in_process` → `approved`)

### 6.2) O que mudar no backend

**1) Validação do request**
- Em `StorePledgeRequest`, manter `payment_method in: pix,card`.
- Para `card`, adicionar validação de:
  - `card_token` (string, required)
  - `installments` (int, opcional; default 1)
  - `payer.email` (já existe via usuário autenticado)
  - (opcional, mas comum em produção) `payer.identification.type/number`

**2) `PledgeController@store`**
- Hoje ele envia `payer_email/description/idempotency_key` no metadata.
- Para `card`, ele deve também repassar `card_token` e `installments` no metadata.

**3) `MercadoPagoPaymentService`**
- Implementar branch `card` em `processPayment()`.
- Chamar `POST /v1/payments` com `payment_method_id` de cartão + `token`.
- Mapear status do MP para `PledgeStatus` (mesma função `mapMercadoPagoStatusToPledgeStatus`).

### 6.3) Payload sugerido do frontend → backend

Exemplo de payload do `POST /api/pledges` (somente o essencial; ajuste conforme necessidade de antifraude):

```json
{
  "campaign_id": 123,
  "amount": "10.00",
  "payment_method": "card",
  "card_token": "<token_gerado_pelo_sdk>",
  "installments": 1
}
```

Observação: o backend já conhece o `payer_email` via usuário autenticado (sessão), então não precisa aceitar email do client.

### 6.4) Payload sugerido do backend → Mercado Pago (cartão)

Sem copiar a referência oficial, a estrutura geral é:

```json
{
  "transaction_amount": 10.00,
  "description": "Apoio campanha #123",
  "token": "<card_token>",
  "installments": 1,
  "payer": { "email": "usuario@exemplo.com" },
  "external_reference": "pledge_999"
}
```

O `payment_method_id` (bandeira) pode ser inferido pelo token/SDK (dependendo do fluxo), ou enviado explicitamente se o SDK retornar.

### 6.5) Frontend (SPA) — como encaixar

Hoje a UI de cartão é *mock* e coleta campos (número/nome/validade/cvv) apenas para UX.

Para produção:
- manter a UI, mas ao enviar:
  - chamar o SDK do MP para tokenizar
  - enviar apenas `card_token` + `installments`
- remover (ou garantir que não são enviados) os campos brutos do cartão no payload da API

### 6.6) Segurança e operação

- O webhook deve ser tratado como “fonte de verdade” (status final). Mesmo em cartão, pode haver transições.
- Para reduzir duplicidade de cobranças:
  - manter `X-Idempotency-Key` fixo por pledge (`pledge_{id}` já existe)
- Recomendado antes de ir para produção:
  - validar assinatura/segredo do webhook (há `MERCADOPAGO_WEBHOOK_SECRET` reservado no `.env`, ainda não usado no código)

## 7) Teste local do webhook

Como o Mercado Pago precisa chamar seu webhook, em ambiente local você precisa expor uma URL pública.

Opções comuns:
- `ngrok http 8000`
- `cloudflared tunnel`

Depois:
1. Ajuste `APP_URL` para o host público
2. Garanta que `MERCADOPAGO_WEBHOOK_URL` aponte para `APP_URL/api/webhooks/mercadopago`
3. Configure a mesma URL no painel do Mercado Pago

## 8) Troubleshooting

- Pagamento não confirma:
  - Verifique logs (`storage/logs/laravel.log`) e se o webhook está chegando
  - Confira se `MERCADOPAGO_ACCESS_TOKEN` está válido
- Webhook chega mas não encontra pledge:
  - Verifique se o `provider` do pledge está `mercadopago`
  - Verifique se `provider_payment_id` foi salvo no `store()`
- Duplicação de pagamentos:
  - Confirme se `X-Idempotency-Key` está fixo por pledge (já está: `pledge_{id}`)
