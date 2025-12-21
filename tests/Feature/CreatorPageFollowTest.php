<?php

namespace Tests\Feature;

use App\Actions\Campaign\PublishCampaign;
use App\Domain\Campaign\Campaign;
use App\Enums\CampaignStatus;
use App\Models\CreatorPage;
use App\Models\User;
use App\Notifications\NewCampaignPublished;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CreatorPageFollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_follow_and_unfollow_a_creator_page(): void
    {
        $creator = User::factory()->create();
        $supporter = User::factory()->create();

        $page = CreatorPage::ensureDefaultForUser($creator);

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        $response = $this->actingAs($supporter)->postJson("/api/creator-pages/{$page->slug}/follow");
        $response->assertOk();
        $this->assertTrue($supporter->fresh()->followingCreatorPages()->whereKey($page->id)->exists());

        $response = $this->actingAs($supporter)->deleteJson("/api/creator-pages/{$page->slug}/follow");
        $response->assertOk();
        $this->assertFalse($supporter->fresh()->followingCreatorPages()->whereKey($page->id)->exists());
    }

    public function test_follower_gets_notified_when_page_publishes_campaign(): void
    {
        Notification::fake();

        $creator = User::factory()->create();
        $supporter = User::factory()->create();

        $page = CreatorPage::ensureDefaultForUser($creator);
        $supporter->followingCreatorPages()->attach($page->id);

        $campaign = Campaign::query()->create([
            'user_id' => $creator->id,
            'creator_page_id' => $page->id,
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
