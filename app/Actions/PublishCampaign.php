<?php

namespace App\Actions;

use App\Domain\Campaign\Campaign;
use Exception;

class PublishCampaign
{
    public function execute(Campaign $campaign): bool
    {
        // Validações
        if ($campaign->status !== 'draft') {
            throw new Exception('Apenas campanhas em rascunho podem ser publicadas.');
        }

        if (empty($campaign->title) || empty($campaign->description)) {
            throw new Exception('Campanha precisa ter título e descrição.');
        }

        if ($campaign->goal_amount <= 0) {
            throw new Exception('Meta da campanha deve ser maior que zero.');
        }

        if (!$campaign->ends_at) {
            throw new Exception('Campanha precisa ter uma data de término.');
        }

        // Publicar
        $campaign->status = 'active';

        if (!$campaign->starts_at) {
            $campaign->starts_at = now();
        }

        return $campaign->save();
    }
}
