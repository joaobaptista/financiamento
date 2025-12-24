<?php

namespace App\Actions\Pledge;

use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Reward;
use App\Domain\Pledge\Pledge;
use App\Enums\CampaignStatus;
use App\Enums\PledgeStatus;
use App\Notifications\CampaignGoalReached;
use Illuminate\Support\Facades\DB;

class ConfirmPayment
{
    public function execute(Pledge $pledge, ?string $paymentId = null): bool
    {
        return (bool) DB::transaction(function () use ($pledge, $paymentId) {
            $lockedPledge = Pledge::query()
                ->whereKey($pledge->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPledge->status === PledgeStatus::Paid) {
                return true;
            }

            // Decrement reward stock only when payment is confirmed
            if ($lockedPledge->reward_id) {
                $rewardId = (int) $lockedPledge->reward_id;

                // Unlimited rewards (quantity=null) do not decrement
                $isLimited = Reward::query()->whereKey($rewardId)->whereNotNull('quantity')->exists();

                if ($isLimited) {
                    $updated = Reward::query()
                        ->whereKey($rewardId)
                        ->where('remaining', '>', 0)
                        ->decrement('remaining', 1);

                    if ($updated !== 1) {
                        throw new \RuntimeException('Esta recompensa não está mais disponível.');
                    }
                }
            }

            $lockedPledge->markAsPaid($paymentId);

            /** @var Campaign $campaign */
            $campaign = Campaign::query()
                ->whereKey($lockedPledge->campaign_id)
                ->lockForUpdate()
                ->firstOrFail();

            $wasBelowGoal = (int) $campaign->pledged_amount < (int) $campaign->goal_amount;
            $campaign->pledged_amount = (int) $campaign->pledged_amount + (int) $lockedPledge->amount;
            $campaign->save();

            $crossedGoal = $wasBelowGoal && (int) $campaign->pledged_amount >= (int) $campaign->goal_amount;
            $shouldNotifyGoalReached =
                $crossedGoal &&
                $campaign->status === CampaignStatus::Active &&
                $campaign->goal_reached_notified_at === null;

            if ($shouldNotifyGoalReached) {
                $campaign->goal_reached_notified_at = now();
                $campaign->save();

                DB::afterCommit(function () use ($campaign): void {
                    $campaign->loadMissing('user');
                    if ($campaign->user) {
                        $campaign->user->notify(new CampaignGoalReached($campaign));
                    }
                });
            }

            return true;
        });
    }
}
