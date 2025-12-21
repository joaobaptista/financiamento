<?php

namespace App\Actions\Campaign;

use App\Domain\Campaign\Campaign;

class PublishCampaign
{
    public function execute(Campaign $campaign): bool
    {
        if ($campaign->status !== 'draft') {
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

        $campaign->status = 'active';

        if (!$campaign->starts_at) {
            $campaign->starts_at = now();
        }

        return $campaign->save();
    }
}
