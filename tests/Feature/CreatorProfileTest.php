<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatorProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_creator_profile_category(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/me/creator-profile', [
            'primary_category' => 'musica',
            'subcategory' => 'Geral',
        ]);

        $response->assertOk();

        $user->refresh();

        $this->assertNotNull($user->creatorProfile);
        $this->assertSame('musica', $user->creatorProfile->primary_category);
        $this->assertSame('Geral', $user->creatorProfile->subcategory);
    }
}
