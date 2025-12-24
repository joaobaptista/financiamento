<?php

namespace Tests\Feature;

use App\Domain\Campaign\Campaign;
use App\Domain\Pledge\Pledge;
use App\Enums\CampaignStatus;
use App\Enums\PledgeStatus;
use App\Jobs\FinalizeEndedCampaignsJob;
use App\Jobs\SendCampaignEndingSoonNotificationsJob;
use App\Models\CreatorPage;
use App\Models\User;
use App\Notifications\CampaignEndingSoon;
use App\Notifications\CampaignGoalReached;
use App\Notifications\CampaignSuccessful;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CampaignLifecycleNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_notifies_followers_when_campaign_is_ending_soon_only_once(): void
    {
        Notification::fake();

        $creator = User::factory()->create();
        $follower = User::factory()->create();

        $page = CreatorPage::ensureDefaultForUser($creator);
        $follower->followingCreatorPages()->attach($page->id);

        $campaign = Campaign::query()->create([
            'user_id' => $creator->id,
            'creator_page_id' => $page->id,
            'title' => 'Campanha terminando',
            'slug' => 'campanha-terminando',
            'description' => 'Descricao',
            'goal_amount' => 10000,
            'pledged_amount' => 0,
            'ends_at' => now()->addDays(2),
            'status' => CampaignStatus::Active,
            'ending_soon_notified_at' => null,
        ]);

        config()->set('campaigns.ending_soon.days', 3);

        (new SendCampaignEndingSoonNotificationsJob())->handle();
        Notification::assertSentTo($follower, CampaignEndingSoon::class);

        Notification::fake();
        (new SendCampaignEndingSoonNotificationsJob())->handle();
        Notification::assertNothingSent();

        $this->assertNotNull($campaign->fresh()->ending_soon_notified_at);
    }

    public function test_it_finalizes_ended_campaign_and_notifies_creator_and_supporter_only_once(): void
    {
        Notification::fake();

        $creator = User::factory()->create();
        $supporter = User::factory()->create();

        $page = CreatorPage::ensureDefaultForUser($creator);

        $campaign = Campaign::query()->create([
            'user_id' => $creator->id,
            'creator_page_id' => $page->id,
            'title' => 'Campanha finalizada',
            'slug' => 'campanha-finalizada',
            'description' => 'Descricao',
            'goal_amount' => 10000,
            'pledged_amount' => 15000,
            'ends_at' => now()->subDay(),
            'status' => CampaignStatus::Active,
            'finished_notified_at' => null,
        ]);

        Pledge::query()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $supporter->id,
            'reward_id' => null,
            'amount' => 15000,
            'status' => PledgeStatus::Paid,
            'payment_method' => 'card',
            'provider' => 'mock',
            'provider_payment_id' => 'pay_test_1',
            'provider_payload' => null,
            'paid_at' => now()->subDays(2),
        ]);

        (new FinalizeEndedCampaignsJob())->handle();

        $campaign->refresh();
        $this->assertSame(CampaignStatus::Successful->value, $campaign->status->value);
        $this->assertNotNull($campaign->finished_notified_at);

        Notification::assertSentTo($creator, CampaignSuccessful::class);
        Notification::assertSentTo($supporter, CampaignSuccessful::class);

        Notification::fake();
        (new FinalizeEndedCampaignsJob())->handle();
        Notification::assertNothingSent();
    }

    public function test_it_notifies_creator_when_campaign_reaches_goal_only_once(): void
    {
        Notification::fake();

        $creator = User::factory()->create();
        $supporter = User::factory()->create();

        $page = CreatorPage::ensureDefaultForUser($creator);

        $campaign = Campaign::query()->create([
            'user_id' => $creator->id,
            'creator_page_id' => $page->id,
            'title' => 'Campanha meta',
            'slug' => 'campanha-meta',
            'description' => 'Descricao',
            'goal_amount' => 10000,
            'pledged_amount' => 9000,
            'ends_at' => now()->addDays(10),
            'status' => CampaignStatus::Active,
            'goal_reached_notified_at' => null,
        ]);

        $pledge = Pledge::query()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $supporter->id,
            'reward_id' => null,
            'amount' => 1500,
            'status' => PledgeStatus::Pending,
            'payment_method' => 'pix',
            'provider' => 'mock',
            'provider_payment_id' => 'pay_test_goal_1',
            'provider_payload' => null,
        ]);

        app(\App\Actions\Pledge\ConfirmPayment::class)->execute($pledge, 'pay_test_goal_1');

        Notification::assertSentTo($creator, CampaignGoalReached::class);
        $this->assertNotNull($campaign->fresh()->goal_reached_notified_at);

        Notification::fake();

        // Second pledge should not trigger again
        $pledge2 = Pledge::query()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $supporter->id,
            'reward_id' => null,
            'amount' => 100,
            'status' => PledgeStatus::Pending,
            'payment_method' => 'pix',
            'provider' => 'mock',
            'provider_payment_id' => 'pay_test_goal_2',
            'provider_payload' => null,
        ]);

        app(\App\Actions\Pledge\ConfirmPayment::class)->execute($pledge2, 'pay_test_goal_2');

        Notification::assertNothingSent();
    }
}
