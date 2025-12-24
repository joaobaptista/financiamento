<?php

namespace App\Jobs;

use App\Domain\Campaign\Campaign;
use App\Enums\CampaignStatus;
use App\Notifications\CampaignEndingSoon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendCampaignEndingSoonNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $days = (int) config('campaigns.ending_soon.days', 3);
        if ($days < 1) {
            return;
        }

        $now = now();
        $from = $now->copy();
        $to = $now->copy()->addDays($days);

        Campaign::query()
            ->where('status', CampaignStatus::Active->value)
            ->whereNotNull('ends_at')
            ->whereNull('ending_soon_notified_at')
            ->whereBetween('ends_at', [$from, $to])
            ->with(['creatorPage', 'user'])
            ->orderBy('id')
            ->lazyById(100)
            ->each(function (Campaign $campaign): void {
                $page = $campaign->creatorPage;

                if ($page) {
                    $page->followers()
                        ->select('users.*')
                        ->chunkById(200, function ($followers) use ($campaign) {
                            Notification::send($followers, new CampaignEndingSoon($campaign));
                        });
                }

                $campaign->ending_soon_notified_at = now();
                $campaign->save();
            });
    }
}
