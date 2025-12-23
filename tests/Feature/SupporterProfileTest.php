<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class SupporterProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_supporter_profile(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();

        $res = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/me/supporter-profile', [
                'postal_code' => '90050102',
                'address_street' => 'Rua General Lima e Silva',
                'address_number' => '111',
                'address_complement' => 'apto 101',
                'address_neighborhood' => 'Cidade Baixa',
                'address_city' => 'Porto Alegre',
                'address_state' => 'RS',
                'phone' => '51993573343',
            ]);

        $res->assertOk();
        $res->assertJson(['ok' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'postal_code' => '90050102',
            'address_city' => 'Porto Alegre',
            'address_state' => 'RS',
        ]);
    }
}
