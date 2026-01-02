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
            'creator_page_id' => $data->creatorPageId,
            'title' => $data->title,
            'description' => $data->description,
            'niche' => $data->niche,
            'goal_amount' => $data->goalAmount,
            'ends_at' => $data->endsAt,
            'cover_image_path' => $data->coverImagePath,
        ]);

        foreach ($data->rewards as $rewardData) {
            $campaign->rewards()->create([
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
