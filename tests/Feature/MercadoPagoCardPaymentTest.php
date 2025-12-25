<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MercadoPagoCardPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_support_with_card_in_mercadopago_mode_when_payment_is_approved(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        config([
            'payments.driver' => 'mercadopago',
            'mercadopago.base_url' => 'https://api.mercadopago.com',
            'mercadopago.access_token' => 'TEST_ACCESS_TOKEN',
            'mercadopago.webhook_url' => 'https://example.com/api/webhooks/mercadopago',
        ]);

        Http::fake([
            'https://api.mercadopago.com/v1/payments' => Http::response([
                'id' => 123456,
                'status' => 'approved',
            ], 201),
        ]);

        $user = User::factory()->create();
        $endsAt = now()->addDays(10)->toDateString();

        $create = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/me/campaigns', [
                'title' => 'Campanha para apoio cartão MP',
                'description' => 'Descrição',
                'niche' => 'arte',
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
                'card_token' => 'tok_test_123',
                'installments' => 1,
                'payment_method_id' => 'visa',
                'payer_identification_type' => 'CPF',
                'payer_identification_number' => '12345678901',
            ]);

        $pledge->assertCreated();
        $this->assertTrue((bool) $pledge->json('ok'));
        $this->assertSame('paid', $pledge->json('status'));
        $this->assertSame('card', $pledge->json('payment.method'));
        $this->assertSame('paid', $pledge->json('payment.status'));

        Http::assertSent(function ($request) {
            if ($request->url() !== 'https://api.mercadopago.com/v1/payments') {
                return false;
            }

            $data = $request->data();

            return (
                ($data['token'] ?? null) === 'tok_test_123' &&
                ($data['payment_method_id'] ?? null) === 'visa' &&
                (float) ($data['transaction_amount'] ?? 0) === 10.0 &&
                (int) ($data['installments'] ?? 0) === 1
            );
        });
    }

    public function test_confirm_endpoint_is_disabled_in_mercadopago_mode(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        config([
            'payments.driver' => 'mercadopago',
        ]);

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/pledges/1/confirm', []);

        $response->assertStatus(422);
        $this->assertNotEmpty((string) $response->json('message'));
    }
}
