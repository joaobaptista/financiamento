<?php

namespace Database\Seeders;

use App\Models\User;
use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Reward;
use App\Domain\Pledge\Pledge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuários
        $creator1 = User::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ]);

        $creator2 = User::factory()->create([
            'name' => 'Maria Santos',
            'email' => 'maria@example.com',
        ]);

        $backer1 = User::factory()->create([
            'name' => 'Pedro Oliveira',
            'email' => 'pedro@example.com',
        ]);

        $backer2 = User::factory()->create([
            'name' => 'Ana Costa',
            'email' => 'ana@example.com',
        ]);

        $backer3 = User::factory()->create([
            'name' => 'Carlos Souza',
            'email' => 'carlos@example.com',
        ]);

        // Campanha 1 - Ativa e bem-sucedida
        $campaign1 = Campaign::create([
            'user_id' => $creator1->id,
            'title' => 'Livro de Contos Fantásticos',
            'slug' => Str::slug('Livro de Contos Fantásticos'),
            'description' => 'Um livro com 20 contos de fantasia originais, ilustrado por artistas brasileiros. O projeto visa publicar uma edição de luxo com capa dura e ilustrações coloridas.',
            'goal_amount' => 5000000, // R$ 50.000,00
            'pledged_amount' => 3500000, // R$ 35.000,00
            'starts_at' => now()->subDays(15),
            'ends_at' => now()->addDays(15),
            'status' => 'active',
            'cover_image_path' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800',
        ]);

        Reward::create([
            'campaign_id' => $campaign1->id,
            'title' => 'Livro Digital',
            'description' => 'Versão digital do livro em PDF',
            'min_amount' => 2000, // R$ 20,00
            'quantity' => 100,
            'remaining' => 85,
        ]);

        Reward::create([
            'campaign_id' => $campaign1->id,
            'title' => 'Livro Físico',
            'description' => 'Livro impresso com capa dura',
            'min_amount' => 5000, // R$ 50,00
            'quantity' => 50,
            'remaining' => 30,
        ]);

        // Campanha 2 - Ativa
        $campaign2 = Campaign::create([
            'user_id' => $creator2->id,
            'title' => 'Documentário sobre Música Brasileira',
            'slug' => Str::slug('Documentário sobre Música Brasileira'),
            'description' => 'Um documentário que explora a riqueza da música brasileira, desde o samba até o funk carioca, com entrevistas exclusivas com grandes artistas.',
            'goal_amount' => 10000000, // R$ 100.000,00
            'pledged_amount' => 2500000, // R$ 25.000,00
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(20),
            'status' => 'active',
            'cover_image_path' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=800',
        ]);

        Reward::create([
            'campaign_id' => $campaign2->id,
            'title' => 'Acesso Antecipado',
            'description' => 'Assista ao documentário antes do lançamento oficial',
            'min_amount' => 3000, // R$ 30,00
            'quantity' => null,
            'remaining' => null,
        ]);

        // Campanha 3 - Ativa
        $campaign3 = Campaign::create([
            'user_id' => $creator1->id,
            'title' => 'Jogo de Tabuleiro Cooperativo',
            'slug' => Str::slug('Jogo de Tabuleiro Cooperativo'),
            'description' => 'Um jogo de tabuleiro cooperativo ambientado no Brasil colonial, onde os jogadores trabalham juntos para construir uma comunidade sustentável.',
            'goal_amount' => 3000000, // R$ 30.000,00
            'pledged_amount' => 1200000, // R$ 12.000,00
            'starts_at' => now()->subDays(5),
            'ends_at' => now()->addDays(25),
            'status' => 'active',
            'cover_image_path' => 'https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?w=800',
        ]);

        Reward::create([
            'campaign_id' => $campaign3->id,
            'title' => 'Jogo Completo',
            'description' => 'Uma cópia do jogo completo',
            'min_amount' => 15000, // R$ 150,00
            'quantity' => 100,
            'remaining' => 92,
        ]);

        // Campanha 4 - Draft
        Campaign::create([
            'user_id' => $creator2->id,
            'title' => 'Podcast sobre Tecnologia',
            'slug' => Str::slug('Podcast sobre Tecnologia'),
            'description' => 'Um podcast semanal discutindo as últimas tendências em tecnologia e inovação.',
            'goal_amount' => 2000000, // R$ 20.000,00
            'pledged_amount' => 0,
            'starts_at' => null,
            'ends_at' => now()->addDays(30),
            'status' => 'draft',
        ]);

        // Criar pledges
        $pledges = [
            ['campaign_id' => $campaign1->id, 'user_id' => $backer1->id, 'amount' => 5000, 'reward_id' => 2],
            ['campaign_id' => $campaign1->id, 'user_id' => $backer2->id, 'amount' => 2000, 'reward_id' => 1],
            ['campaign_id' => $campaign1->id, 'user_id' => $backer3->id, 'amount' => 10000, 'reward_id' => null],
            ['campaign_id' => $campaign2->id, 'user_id' => $backer1->id, 'amount' => 3000, 'reward_id' => 3],
            ['campaign_id' => $campaign2->id, 'user_id' => $backer3->id, 'amount' => 5000, 'reward_id' => null],
            ['campaign_id' => $campaign3->id, 'user_id' => $backer2->id, 'amount' => 15000, 'reward_id' => 4],
        ];

        foreach ($pledges as $pledgeData) {
            Pledge::create([
                'campaign_id' => $pledgeData['campaign_id'],
                'user_id' => $pledgeData['user_id'],
                'reward_id' => $pledgeData['reward_id'],
                'amount' => $pledgeData['amount'],
                'status' => 'paid',
                'provider' => 'mock',
                'provider_payment_id' => 'mock_' . uniqid(),
                'paid_at' => now()->subDays(rand(1, 10)),
            ]);
        }

        $this->command->info('Database seeded successfully!');
    }
}
