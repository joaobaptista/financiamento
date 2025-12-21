<?php

namespace App\Actions\Dashboard;

use App\Data\Dashboard\CampaignStatsData;
use App\Domain\Campaign\Campaign;

class GetCampaignDashboardData
{
    /**
     * @return array{campaign: Campaign, stats: CampaignStatsData}
     */
    public function execute(int $userId, int $campaignId): array
    {
        $campaign = Campaign::query()
            ->where('id', $campaignId)
            ->where('user_id', $userId)
            ->with([
                'pledges.user',
                'pledges' => function ($query) {
                    $query->where('status', 'paid')->orderBy('paid_at', 'desc');
                },
            ])
            ->firstOrFail();

        $stats = new CampaignStatsData(
            totalBackers: $campaign->pledges()->where('status', 'paid')->count(),
            totalRaised: $campaign->pledged_amount,
            progress: $campaign->calculateProgress(),
            daysRemaining: $campaign->daysRemaining(),
        );

        return [
            'campaign' => $campaign,
            'stats' => $stats,
        ];
    }
}
