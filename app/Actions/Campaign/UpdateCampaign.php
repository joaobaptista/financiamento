<?php

namespace App\Actions\Campaign;

use App\Data\Campaign\UpdateCampaignData;
use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Reward;
use App\Enums\CampaignStatus;

class UpdateCampaign
{
    public function execute(UpdateCampaignData $data): Campaign
    {
        $campaign = Campaign::query()
            ->where('id', $data->campaignId)
            ->where('user_id', $data->userId)
            ->firstOrFail();

        if (!in_array($campaign->status, [CampaignStatus::Draft, CampaignStatus::Active], true)) {
            throw new \RuntimeException('Apenas campanhas em rascunho ou ativas podem ser editadas.');
        }

        $campaign->update([
            'creator_page_id' => $data->creatorPageId,
            'title' => $data->title,
            'description' => $data->description,
            'niche' => $data->niche,
            'goal_amount' => $data->goalAmount,
            'ends_at' => $data->endsAt,
            'cover_image_path' => $data->coverImagePath,
        ]);

        $campaign->rewards()->delete();

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
