<?php

namespace Tests\Feature;

use App\Actions\Campaign\PublishCampaign;
use App\Domain\Campaign\Campaign;
use App\Enums\CampaignStatus;
use App\Models\User;
use App\Notifications\NewCampaignPublished;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CreatorSupportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_support_and_unsupport_a_creator(): void
    {
        $creator = User::factory()->create();
        $supporter = User::factory()->create();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        $response = $this->actingAs($supporter)->postJson("/api/creators/{$creator->id}/support");
        $response->assertOk();
        $this->assertTrue($supporter->fresh()->supportedCreators()->whereKey($creator->id)->exists());

        $response = $this->actingAs($supporter)->deleteJson("/api/creators/{$creator->id}/support");
        $response->assertOk();
        $this->assertFalse($supporter->fresh()->supportedCreators()->whereKey($creator->id)->exists());
    }

    public function test_supporter_gets_notified_when_creator_publishes_campaign(): void
    {
        Notification::fake();

        $creator = User::factory()->create();
        $supporter = User::factory()->create();
        $supporter->supportedCreators()->attach($creator->id);

        $campaign = Campaign::query()->create([
            'user_id' => $creator->id,
            'title' => 'Nova campanha',
            'slug' => 'nova-campanha',
            'description' => 'Descricao',
            'goal_amount' => 10000,
            'pledged_amount' => 0,
            'ends_at' => now()->addDays(10),
            'status' => CampaignStatus::Draft,
        ]);

        $action = app(PublishCampaign::class);
        $action->execute($campaign);

        Notification::assertSentTo($supporter, NewCampaignPublished::class);
    }
}
