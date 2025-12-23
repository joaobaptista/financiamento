<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class PledgePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_support_with_card_in_mock_mode_and_it_is_confirmed_immediately(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();
        $endsAt = now()->addDays(10)->toDateString();

        $create = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/me/campaigns', [
                'title' => 'Campanha para apoio cartão',
                'description' => 'Descrição',
                'goal_amount' => '10.00',
                'ends_at' => $endsAt,
            ]);

        $create->assertCreated();
        $campaignId = (int) $create->json('data.id');

        $publish = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post("/api/me/campaigns/{$campaignId}/publish", []);

        $publish->assertOk();

        $pledge = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/pledges', [
                'campaign_id' => $campaignId,
                'amount' => '10.00',
                'payment_method' => 'card',
                // token is optional in mock mode but should be accepted
                'card_token' => 'mock_card_test_token',
                'installments' => 1,
            ]);

        $pledge->assertCreated();
        $this->assertTrue((bool) $pledge->json('ok'));
        $this->assertSame('paid', $pledge->json('status'));
        $this->assertSame('card', $pledge->json('payment.method'));
        $this->assertSame('paid', $pledge->json('payment.status'));
        $this->assertNotEmpty((string) $pledge->json('payment.payment_id'));
    }

    public function test_user_can_support_with_pix_in_mock_mode_and_it_returns_next_action(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();
        $endsAt = now()->addDays(10)->toDateString();

        $create = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/me/campaigns', [
                'title' => 'Campanha para apoio pix',
                'description' => 'Descrição',
                'goal_amount' => '10.00',
                'ends_at' => $endsAt,
            ]);

        $create->assertCreated();
        $campaignId = (int) $create->json('data.id');

        $publish = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post("/api/me/campaigns/{$campaignId}/publish", []);

        $publish->assertOk();

        $pledge = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/pledges', [
                'campaign_id' => $campaignId,
                'amount' => '10.00',
                'payment_method' => 'pix',
            ]);

        $pledge->assertCreated();
        $this->assertTrue((bool) $pledge->json('ok'));
        $this->assertSame('pending', $pledge->json('status'));
        $this->assertSame('pix', $pledge->json('payment.method'));
        $this->assertSame('pending', $pledge->json('payment.status'));
        $this->assertNotEmpty((string) $pledge->json('payment.payment_id'));
        $this->assertSame('pix', $pledge->json('payment.next_action.type'));
        $this->assertNotEmpty((string) $pledge->json('payment.next_action.copy_paste'));
    }
}
