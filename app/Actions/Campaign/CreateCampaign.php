<?php

namespace App\Actions\Campaign;

use App\Data\Campaign\CreateCampaignData;
use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Reward;

class CreateCampaign
{
    public function execute(CreateCampaignData $data): Campaign
    {
        $campaign = Campaign::create([
            'user_id' => $data->userId,
            'title' => $data->title,
            'description' => $data->description,
            'goal_amount' => $data->goalAmount,
            'ends_at' => $data->endsAt,
            'cover_image_path' => $data->coverImagePath,
        ]);

        foreach ($data->rewards as $rewardData) {
            Reward::create([
                'campaign_id' => $campaign->id,
                'title' => $rewardData->title,
                'description' => $rewardData->description,
                'min_amount' => $rewardData->minAmount,
                'quantity' => $rewardData->quantity,
                'remaining' => $rewardData->quantity,
            ]);
        }

        return $campaign;
    }
}
