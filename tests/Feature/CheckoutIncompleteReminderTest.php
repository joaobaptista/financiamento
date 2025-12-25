<?php

namespace Tests\Feature;

use App\Domain\Pledge\Pledge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutIncompleteReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_marks_pending_pix_pledges_as_reminded_only_once(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);
        Mail::fake();

        $user = User::factory()->create();
        $endsAt = now()->addDays(10)->toDateString();

        $create = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/me/campaigns', [
                'title' => 'Campanha para lembrete',
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

        $pledgeResponse = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/pledges', [
                'campaign_id' => $campaignId,
                'amount' => '10.00',
                'payment_method' => 'pix',
            ]);

        $pledgeResponse->assertCreated();
        $pledgeId = (int) $pledgeResponse->json('pledge_id');

        // Make it old enough to qualify
        Pledge::query()->whereKey($pledgeId)->update([
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2),
            'checkout_incomplete_reminded_at' => null,
        ]);

        config()->set('payments.checkout_incomplete.delay_minutes', 60);

        // First run should mark it
        (new \App\Jobs\SendCheckoutIncompleteRemindersJob())->handle();

        $pledge = Pledge::query()->findOrFail($pledgeId);
        $this->assertNotNull($pledge->checkout_incomplete_reminded_at);

        $firstMarkedAt = $pledge->checkout_incomplete_reminded_at;

        // Second run should not change it
        (new \App\Jobs\SendCheckoutIncompleteRemindersJob())->handle();

        $pledge->refresh();
        $this->assertEquals($firstMarkedAt, $pledge->checkout_incomplete_reminded_at);
    }
}
