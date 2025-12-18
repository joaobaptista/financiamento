<?php

namespace Database\Seeders;

use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Reward;
use App\Domain\Pledge\Pledge;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
                'description' => 'Campanha de exemplo para desenvolvimento local. Um livro com contos de fantasia, edição de luxo e ilustrações coloridas.',
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
                'description' => 'Campanha de exemplo para desenvolvimento local. Um jogo cooperativo com cenário brasileiro, cartas, miniaturas e modo solo.',
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
        foreach ([$campaignBook, $campaignGame, $campaignDraft] as $campaign) {
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
