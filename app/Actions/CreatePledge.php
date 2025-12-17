<?php

namespace App\Actions;

use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Reward;
use App\Domain\Pledge\Pledge;
use App\Models\User;
use Exception;

class CreatePledge
{
    public function execute(Campaign $campaign, User $user, int $amount, ?int $rewardId = null): Pledge
    {
        // Validações
        if ($campaign->status !== 'active') {
            throw new Exception('Apenas campanhas ativas podem receber apoios.');
        }

        if ($campaign->isExpired()) {
            throw new Exception('Esta campanha já expirou.');
        }

        $reward = null;
        if ($rewardId) {
            $reward = Reward::findOrFail($rewardId);

            if ($reward->campaign_id !== $campaign->id) {
                throw new Exception('Recompensa não pertence a esta campanha.');
            }

            if (!$reward->isAvailable()) {
                throw new Exception('Esta recompensa não está mais disponível.');
            }

            if ($amount < $reward->min_amount) {
                throw new Exception('Valor mínimo para esta recompensa é ' . $reward->formatted_min_amount);
            }
        }

        // Criar pledge
        $pledge = new Pledge([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'reward_id' => $rewardId,
            'amount' => $amount,
            'status' => 'pending',
            'provider' => 'mock',
        ]);

        $pledge->save();

        // Decrementar quantidade da recompensa se aplicável
        if ($reward) {
            $reward->decrementRemaining();
        }

        return $pledge;
    }
}
