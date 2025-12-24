<?php

namespace App\Jobs;

use App\Domain\Campaign\Campaign;
use App\Domain\Pledge\Pledge;
use App\Enums\CampaignStatus;
use App\Enums\PledgeStatus;
use App\Models\User;
use App\Notifications\CampaignFailed;
use App\Notifications\CampaignSuccessful;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class FinalizeEndedCampaignsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $now = now();

        Campaign::query()
            ->where('status', CampaignStatus::Active->value)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $now)
            ->orderBy('id')
            ->lazyById(100)
            ->each(function (Campaign $campaign): void {
                $newStatus = $campaign->pledged_amount >= $campaign->goal_amount
                    ? CampaignStatus::Successful->value
                    : CampaignStatus::Failed->value;

                $campaign->status = $newStatus;
                $campaign->save();

                if ($campaign->finished_notified_at) {
                    return;
                }

                $campaign->loadMissing(['user']);

                $notification = $newStatus === CampaignStatus::Successful->value
                    ? new CampaignSuccessful($campaign)
                    : new CampaignFailed($campaign);

                // Notify creator
                if ($campaign->user) {
                    $campaign->user->notify($notification);
                }

                // Notify supporters (paid pledges)
                Pledge::query()
                    ->where('campaign_id', $campaign->id)
                    ->where('status', PledgeStatus::Paid->value)
                    ->select('user_id')
                    ->distinct()
                    ->orderBy('user_id')
                    ->chunkById(200, function ($rows) use ($notification) {
                        $userIds = $rows->pluck('user_id')->filter()->values();
                        if ($userIds->isEmpty()) {
                            return;
                        }

                        $users = User::query()->whereIn('id', $userIds)->get();
                        Notification::send($users, $notification);
                    }, 'user_id');

                $campaign->finished_notified_at = now();
                $campaign->save();
            });
    }
}
