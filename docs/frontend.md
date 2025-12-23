# Frontend (Vue 3 SPA)

## Stack
- Vue 3
- Vite
- Vue Router (history mode)
- Bootstrap 5 (UI)
- i18n em `resources/js/spa/i18n.js`
- SEO client-side em `resources/js/spa/seo.js`

## Entry points
- `resources/js/spa.js`: inicializa app, router e integrações (axios/SEO).
- `resources/views/spa.blade.php`: HTML base que carrega os assets do Vite.

## Rotas (Vue Router)
Arquivo: `resources/js/spa/routes.js`

Principais telas:
- `/` (home)
- `/campaigns` (listagem)
- `/campaigns/:slug` (detalhe e apoio)
- Páginas institucionais simples (ex.: `/about`, `/how-it-works`, `/terms`, `/privacy` etc.) via `StaticPageView.vue`
- `/login` e `/register`
- `/profile` (perfil do usuário)
- `/dashboard` e `/dashboard/campaigns/:id`
- `/me/creator/setup`
- `/me/campaigns/create` e `/me/campaigns/:id/edit`

Observação: embora a SPA tenha rotas “privadas”, a proteção efetiva depende do backend (rotas GET em `routes/web.php` usam middleware `auth` para algumas telas).

## Comunicação com API
- A SPA consome endpoints em `/api`.
- Como o backend usa sessão/cookies, o client deve:
  - enviar cookies (`withCredentials`)
  - respeitar CSRF em requests state-changing (POST/PUT/DELETE)

Arquivos relevantes:
- `resources/js/spa/api.js` (wrapper de chamadas)

## Pagamento e “conferência do apoiador”
Fluxo esperado na tela de campanha:
- Antes de pagar, a SPA valida se o perfil de apoiador está completo (`/api/me/supporter-profile`).
- Para ajudar no preenchimento, a SPA busca endereço por CEP (`/api/cep/{cep}`) e preenche campos.
- Ao criar apoio:
  - `POST /api/pledges`
  - backend devolve `payment.next_action` para PIX (copia/cola)
  - no driver mock, pode haver botão de confirmação (`POST /api/pledges/{id}/confirm`)

## SEO (client-side)
- Metas são aplicadas usando `meta` do route (titleKey/descriptionKey/robots/ogType).
- Para páginas de campanha, parte do SEO/OG também é complementada no server side (ver `AppServiceProvider`).

## Build e assets
- Vite gera assets em `public/build`.
- Imagens do storage (avatar/capas) são servidas por `/storage/...`.

## Observação de ambiente
Se o `npm run dev` acusar incompatibilidade de Node (Vite requer Node >= 20.19 ou 22.12), atualize o Node para uma versão suportada.
