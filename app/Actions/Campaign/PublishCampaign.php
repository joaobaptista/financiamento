<?php

namespace App\Actions\Campaign;

use App\Domain\Campaign\Campaign;
use App\Enums\CampaignStatus;
use App\Notifications\NewCampaignPublished;
use Illuminate\Support\Facades\Notification;

class PublishCampaign
{
    public function execute(Campaign $campaign): bool
    {
        if ($campaign->status !== CampaignStatus::Draft) {
            throw new \RuntimeException('Apenas campanhas em rascunho podem ser publicadas.');
        }

        if (empty($campaign->title) || empty($campaign->description)) {
            throw new \RuntimeException('Campanha precisa ter título e descrição.');
        }

        if ($campaign->goal_amount <= 0) {
            throw new \RuntimeException('Meta da campanha deve ser maior que zero.');
        }

        if (!$campaign->ends_at) {
            throw new \RuntimeException('Campanha precisa ter uma data de término.');
        }

        $campaign->status = CampaignStatus::Active;

        if (!$campaign->starts_at) {
            $campaign->starts_at = now();
        }

        $saved = $campaign->save();

        if ($saved) {
            $campaign->loadMissing(['creatorPage', 'user']);

            $page = $campaign->creatorPage;

            if ($page) {
                $page->followers()
                    ->select('users.*')
                    ->chunkById(200, function ($followers) use ($campaign) {
                        Notification::send($followers, new NewCampaignPublished($campaign));
                    });
            }
        }

        return $saved;
    }
}
