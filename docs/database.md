# Banco de dados

## Visão geral do schema
O banco é gerenciado por migrations em `database/migrations`.

Principais entidades:
- **users**: autenticação e perfil (inclui endereço/telefone e avatar)
- **campaigns**: campanhas de crowdfunding
- **rewards**: recompensas vinculadas a campanhas
- **pledges**: apoios/pagamentos
- **creator_profiles**: dados do criador (categoria)
- **creator_pages**: “página do criador” com slug
- **creator_page_followers**: seguidores de páginas de criador (pivot)
- **creator_supporters**: apoiadores de criadores (pivot)
- **notifications**: notificações in-app (padrão Laravel)

## Tabelas principais

### campaigns
Migration base: `2025_12_17_131814_create_campaigns_table.php`
Campos:
- `user_id` (FK users)
- `title`, `slug` (unique), `description`
- `goal_amount` (centavos), `pledged_amount` (centavos)
- `starts_at`, `ends_at`
- `status` (enum: `draft|active|successful|failed|cancelled`)
- `cover_image_path`

Evoluções relevantes:
- `creator_page_id` (FK para `creator_pages`, nullable, `nullOnDelete`) com backfill automático.
- `cover_image_webp_path` (migration adicional no projeto)

Índices:
- `status, ends_at`
- `slug`

### rewards
Migration: `2025_12_17_131815_create_rewards_table.php`
Campos:
- `campaign_id` (FK)
- `title`, `description`
- `min_amount` (centavos)
- `quantity` e `remaining` (null = ilimitado)

### pledges
Migration base: `2025_12_17_131816_create_pledges_table.php`
Campos base:
- `campaign_id`, `user_id`, `reward_id` (nullable)
- `amount` (centavos)
- `status` (enum: `pending|paid|refunded|canceled`)
- `provider` (default `mock`)
- `provider_payment_id` (nullable)
- `paid_at`

Evoluções relevantes:
- `payment_method` (default `card`)
- `provider_payload` (json, nullable)

Índices:
- `campaign_id, status`
- `user_id`
- `payment_method`

## Usuários e perfil

### users (campos adicionais)
Migrations:
- `2025_12_23_000001_add_supporter_contact_fields_to_users_table.php`
  - `postal_code`, `address_street`, `address_number`, `address_complement`, `address_neighborhood`, `address_city`, `address_state`, `phone`
- `2025_12_23_120000_add_profile_photo_path_to_users_table.php`
  - `profile_photo_path`

No modelo `app/Models/User.php`:
- `profile_photo_url` é atributo computado apontando para o disco `public`.

## Criadores

### creator_profiles
Migration: `2025_12_21_180001_create_creator_profiles_table.php`
- `user_id` (unique)
- `primary_category`, `subcategory`

### creator_pages
Migration: `2025_12_21_190001_create_creator_pages_table.php`
- `owner_user_id` (FK users)
- `name`, `slug` (unique)
- `primary_category`, `subcategory`

### creator_page_followers (pivot)
Migration: `2025_12_21_190002_create_creator_page_followers_table.php`
- `creator_page_id` (FK)
- `follower_id` (FK users)
- unique (`creator_page_id`, `follower_id`)

### creator_supporters (pivot)
Migration: `2025_12_21_130001_create_creator_supporters_table.php`
- `creator_id` (FK users)
- `supporter_id` (FK users)
- unique (`creator_id`, `supporter_id`)

## Notificações
Migration: `2025_12_21_130002_create_notifications_table.php`
- `id` (uuid)
- `type`, `notifiable` (morphs), `data`, `read_at`

## Observações
- Valores monetários são armazenados em **centavos** (inteiros/bigint).
- `provider_payload` guarda o payload do provedor (ou `next_action`) para auditoria/debug.
