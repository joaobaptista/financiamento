<?php

namespace App\Actions\Pledge;

use App\Data\Pledge\CreatePledgeData;
use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Reward;
use App\Domain\Pledge\Pledge;
use App\Enums\CampaignStatus;
use App\Enums\PledgeStatus;
use App\Models\User;

class CreatePledge
{
    public function execute(CreatePledgeData $data): Pledge
    {
        $campaign = Campaign::query()->findOrFail($data->campaignId);
        $user = User::query()->findOrFail($data->userId);

        if ($campaign->status !== CampaignStatus::Active) {
            throw new \RuntimeException('Apenas campanhas ativas podem receber apoios.');
        }

        if ($campaign->isExpired()) {
            throw new \RuntimeException('Esta campanha já expirou.');
        }

        $reward = null;
        if ($data->rewardId) {
            $reward = Reward::query()->findOrFail($data->rewardId);

            if ($reward->campaign_id !== $campaign->id) {
                throw new \RuntimeException('Recompensa não pertence a esta campanha.');
            }

            if (!$reward->isAvailable()) {
                throw new \RuntimeException('Esta recompensa não está mais disponível.');
            }

            if ($data->amount < $reward->min_amount) {
                throw new \RuntimeException('Valor mínimo para esta recompensa é ' . $reward->formatted_min_amount);
            }
        }

        $pledge = new Pledge([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'reward_id' => $data->rewardId,
            'amount' => $data->amount,
            'shipping_amount' => $data->shippingAmount,
            'status' => PledgeStatus::Pending,
            'payment_method' => $data->paymentMethod,
            'provider' => (string) config('payments.driver', 'mock'),
        ]);

        $pledge->save();

        return $pledge;
    }
}
