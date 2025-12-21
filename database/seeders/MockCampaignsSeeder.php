<?php

namespace Database\Seeders;

use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Reward;
use App\Domain\Pledge\Pledge;
use App\Models\User;
use Illuminate\Database\Seeder;

class MockCampaignsSeeder extends Seeder
{
    public function run(): void
    {
        // Garantir que os usuários de teste existem
        $creator = User::updateOrCreate(
            ['email' => 'creator.local@example.com'],
            [
                'name' => 'Criador Teste',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        $backer = User::updateOrCreate(
            ['email' => 'backer.local@example.com'],
            [
                'name' => 'Apoiador Teste',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin.local@example.com'],
            [
                'name' => 'Admin Local',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        $campaignBook = Campaign::updateOrCreate(
            ['slug' => 'campanha-exemplo-livro'],
            [
                'user_id' => $creator->id,
                'title' => 'Livro de Contos Fantásticos (Exemplo)',
                'description' => 'Campanha de exemplo para desenvolvimento local. Um livro com contos de fantasia, edição de luxo e ilustrações coloridas. Categoria: publicacao.',
                'goal_amount' => 5000000,
                'pledged_amount' => 0,
                'starts_at' => now()->subDays(7),
                'ends_at' => now()->addDays(21),
                'status' => 'active',
                'cover_image_path' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800',
            ]
        );

        $rewardBookDigital = Reward::updateOrCreate(
            ['campaign_id' => $campaignBook->id, 'title' => 'Livro Digital'],
            [
                'description' => 'Versão digital do livro (PDF).',
                'min_amount' => 2000,
                'quantity' => null,
                'remaining' => null,
            ]
        );

        $rewardBookPhysical = Reward::updateOrCreate(
            ['campaign_id' => $campaignBook->id, 'title' => 'Livro Físico (Capa Dura)'],
            [
                'description' => 'Livro impresso com capa dura + marcador.',
                'min_amount' => 5000,
                'quantity' => 50,
                'remaining' => 50,
            ]
        );

        $campaignGame = Campaign::updateOrCreate(
            ['slug' => 'campanha-exemplo-jogo'],
            [
                'user_id' => $creator->id,
                'title' => 'Jogo de Tabuleiro Cooperativo (Exemplo)',
                'description' => 'Campanha de exemplo para desenvolvimento local. Um jogo cooperativo com cenário brasileiro, cartas, miniaturas e modo solo. Categoria: jogos.',
                'goal_amount' => 3000000,
                'pledged_amount' => 0,
                'starts_at' => now()->subDays(3),
                'ends_at' => now()->addDays(14),
                'status' => 'active',
                'cover_image_path' => 'https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?w=800',
            ]
        );

        $rewardGame = Reward::updateOrCreate(
            ['campaign_id' => $campaignGame->id, 'title' => 'Jogo Completo'],
            [
                'description' => 'Uma cópia do jogo completo (base).',
                'min_amount' => 15000,
                'quantity' => 100,
                'remaining' => 100,
            ]
        );

        $campaignDraft = Campaign::updateOrCreate(
            ['slug' => 'campanha-exemplo-podcast'],
            [
                'user_id' => $creator->id,
                'title' => 'Podcast sobre Tecnologia (Rascunho)',
                'description' => 'Campanha em rascunho para testar o fluxo de criação/publicação.',
                'goal_amount' => 2000000,
                'pledged_amount' => 0,
                'starts_at' => null,
                'ends_at' => now()->addDays(30),
                'status' => 'draft',
                'cover_image_path' => null,
            ]
        );

        // Mais campanhas ativas para preencher a Home (featured + recommended)
        $extraCampaigns = [
            [
                'slug' => 'campanha-exemplo-documentario',
                'title' => 'Documentário: Música Brasileira (Exemplo)',
                'description' => 'Campanha de exemplo. Um documentário com entrevistas e gravações especiais sobre a história da música brasileira.',
                'category_key' => 'musica',
                'goal_amount' => 10000000,
                'starts_at' => now()->subDays(10),
                'ends_at' => now()->addDays(20),
                'cover_image_path' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=800',
            ],
            [
                'slug' => 'campanha-exemplo-curta',
                'title' => 'Curta-metragem Independente (Exemplo)',
                'description' => 'Campanha de exemplo. Um curta-metragem filmado em locações reais, com equipe reduzida e trilha original.',
                'category_key' => 'filme',
                'goal_amount' => 4500000,
                'starts_at' => now()->subDays(6),
                'ends_at' => now()->addDays(18),
                'cover_image_path' => 'https://images.unsplash.com/photo-1524985069026-dd778a71c7b4?w=800',
            ],
            [
                'slug' => 'campanha-exemplo-quadrinhos',
                'title' => 'HQ Autorais: Edição de Colecionador (Exemplo)',
                'description' => 'Campanha de exemplo. Uma HQ autoral em edição de colecionador, com extras, artes e making of.',
                'category_key' => 'quadrinhos',
                'goal_amount' => 6000000,
                'starts_at' => now()->subDays(2),
                'ends_at' => now()->addDays(28),
                'cover_image_path' => 'https://images.unsplash.com/photo-1601645191163-3fc0d5d64e35?w=800',
            ],
            [
                'slug' => 'campanha-exemplo-fotografia',
                'title' => 'Livro de Fotografia Urbana (Exemplo)',
                'description' => 'Campanha de exemplo. Um fotolivro com retratos urbanos e histórias de bastidores de cada clique.',
                'category_key' => 'fotografia',
                'goal_amount' => 3500000,
                'starts_at' => now()->subDays(9),
                'ends_at' => now()->addDays(16),
                'cover_image_path' => 'https://images.unsplash.com/photo-1452587925148-ce544e77e70d?w=800',
            ],
            [
                'slug' => 'campanha-exemplo-teatro',
                'title' => 'Peça de Teatro Itinerante (Exemplo)',
                'description' => 'Campanha de exemplo. Uma peça itinerante com apresentações em praças e centros culturais.',
                'category_key' => 'teatro',
                'goal_amount' => 5200000,
                'starts_at' => now()->subDays(4),
                'ends_at' => now()->addDays(24),
                'cover_image_path' => 'https://images.unsplash.com/photo-1520975958225-5b1739a434b4?w=800',
            ],
            [
                'slug' => 'campanha-exemplo-tecnologia',
                'title' => 'Gadget Sustentável (Exemplo)',
                'description' => 'Campanha de exemplo. Um gadget sustentável pensado para reduzir desperdício no dia a dia.',
                'category_key' => 'tecnologia',
                'goal_amount' => 8000000,
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->addDays(29),
                'cover_image_path' => 'https://images.unsplash.com/photo-1518779578993-ec3579fee39f?w=800',
            ],
            [
                'slug' => 'campanha-exemplo-culinaria',
                'title' => 'Livro de Receitas Regionais (Exemplo)',
                'description' => 'Campanha de exemplo. Um livro com receitas regionais, histórias e fotos de cada prato.',
                'category_key' => 'comida',
                'goal_amount' => 2800000,
                'starts_at' => now()->subDays(8),
                'ends_at' => now()->addDays(12),
                'cover_image_path' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=800',
            ],
        ];

        $extraRewardTemplates = [
            [
                'title' => 'Apoio',
                'description' => 'Apoie o projeto e receba atualizações exclusivas.',
                'min_amount' => 1000,
                'quantity' => null,
                'remaining' => null,
            ],
            [
                'title' => 'Recompensa Principal',
                'description' => 'Receba a recompensa principal do projeto (quando aplicável).',
                'min_amount' => 5000,
                'quantity' => 100,
                'remaining' => 100,
            ],
        ];

        $extraBackers = [
            $backer,
            $admin,
            User::updateOrCreate(['email' => 'backer2.local@example.com'], ['name' => 'Apoiador 2', 'password' => 'password']),
            User::updateOrCreate(['email' => 'backer3.local@example.com'], ['name' => 'Apoiador 3', 'password' => 'password']),
        ];

        foreach ($extraCampaigns as $index => $data) {
            $categoryKey = (string) ($data['category_key'] ?? '');
            $categorySuffix = $categoryKey ? " Categoria: {$categoryKey}." : '';

            $campaign = Campaign::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'user_id' => $creator->id,
                    'title' => $data['title'],
                    'description' => $data['description'].$categorySuffix,
                    'goal_amount' => (int) $data['goal_amount'],
                    'pledged_amount' => 0,
                    'starts_at' => $data['starts_at'],
                    'ends_at' => $data['ends_at'],
                    'status' => 'active',
                    'cover_image_path' => $data['cover_image_path'],
                ]
            );

            $rewards = [];
            foreach ($extraRewardTemplates as $tpl) {
                $rewards[] = Reward::updateOrCreate(
                    ['campaign_id' => $campaign->id, 'title' => $tpl['title']],
                    [
                        'description' => $tpl['description'],
                        'min_amount' => $tpl['min_amount'],
                        'quantity' => $tpl['quantity'],
                        'remaining' => $tpl['remaining'],
                    ]
                );
            }

            // Criar alguns pledges pagos para dar progresso real (determinístico e idempotente)
            $goal = (int) $campaign->goal_amount;
            $target = max(1000, (int) round($goal * (0.18 + (($index % 3) * 0.12))));
            $chunks = [
                (int) round($target * 0.55),
                (int) round($target * 0.30),
                (int) max(1000, $target - (int) round($target * 0.55) - (int) round($target * 0.30)),
            ];

            foreach ($chunks as $i => $amount) {
                $u = $extraBackers[($index + $i) % count($extraBackers)];
                $rewardId = ($i % 2 === 0) ? ($rewards[1]->id ?? null) : ($rewards[0]->id ?? null);

                Pledge::updateOrCreate(
                    ['provider_payment_id' => 'mock_seed_'.$campaign->slug.'_'.($i + 1)],
                    [
                        'campaign_id' => $campaign->id,
                        'user_id' => $u->id,
                        'reward_id' => $rewardId,
                        'amount' => (int) $amount,
                        'status' => 'paid',
                        'provider' => 'mock',
                        'paid_at' => now()->subDays(1 + (($index + $i) % 7)),
                    ]
                );
            }
        }

        // Pledges (mock) - usar provider_payment_id fixo para evitar duplicar
        $pledges = [
            [
                'provider_payment_id' => 'mock_seed_book_1',
                'campaign_id' => $campaignBook->id,
                'user_id' => $backer->id,
                'reward_id' => $rewardBookPhysical->id,
                'amount' => 5000,
                'status' => 'paid',
                'paid_at' => now()->subDays(2),
            ],
            [
                'provider_payment_id' => 'mock_seed_book_2',
                'campaign_id' => $campaignBook->id,
                'user_id' => $admin->id,
                'reward_id' => $rewardBookDigital->id,
                'amount' => 2000,
                'status' => 'paid',
                'paid_at' => now()->subDays(1),
            ],
            [
                'provider_payment_id' => 'mock_seed_game_1',
                'campaign_id' => $campaignGame->id,
                'user_id' => $backer->id,
                'reward_id' => $rewardGame->id,
                'amount' => 15000,
                'status' => 'paid',
                'paid_at' => now()->subDays(1),
            ],
        ];

        foreach ($pledges as $pledgeData) {
            Pledge::updateOrCreate(
                ['provider_payment_id' => $pledgeData['provider_payment_id']],
                [
                    'campaign_id' => $pledgeData['campaign_id'],
                    'user_id' => $pledgeData['user_id'],
                    'reward_id' => $pledgeData['reward_id'],
                    'amount' => $pledgeData['amount'],
                    'status' => $pledgeData['status'],
                    'provider' => 'mock',
                    'paid_at' => $pledgeData['paid_at'],
                ]
            );
        }

        // Sincronizar pledged_amount com a soma dos paid pledges
        $campaignsToSync = Campaign::query()->whereIn('slug', [
            $campaignBook->slug,
            $campaignGame->slug,
            $campaignDraft->slug,
            ...array_map(fn ($c) => $c['slug'], $extraCampaigns),
        ])->get();

        foreach ($campaignsToSync as $campaign) {
            $pledged = (int) Pledge::query()
                ->where('campaign_id', $campaign->id)
                ->where('status', 'paid')
                ->sum('amount');

            $campaign->update(['pledged_amount' => $pledged]);

            // Atualizar remaining (quando quantity não é null)
            foreach ($campaign->rewards()->get() as $reward) {
                if ($reward->quantity === null) {
                    $reward->update(['remaining' => null]);
                    continue;
                }

                $used = (int) Pledge::query()
                    ->where('campaign_id', $campaign->id)
                    ->where('reward_id', $reward->id)
                    ->where('status', 'paid')
                    ->count();

                $remaining = max(0, (int) $reward->quantity - $used);
                $reward->update(['remaining' => $remaining]);
            }
        }

        $this->command?->info('Mock campaigns/rewards/pledges created/updated successfully.');
    }
}
