<?php

namespace App\Actions\Dashboard;

use App\Data\Dashboard\CampaignStatsData;
use App\Domain\Campaign\Campaign;
use App\Domain\Pledge\Pledge;
use App\Enums\PledgeStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetCampaignDashboardData
{
    /**
     * @return array{campaign: Campaign, stats: CampaignStatsData, pledges: LengthAwarePaginator}
     */
    public function execute(int $userId, int $campaignId, int $perPage = 25): array
    {
        $campaign = Campaign::query()
            ->where('id', $campaignId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $pledges = Pledge::query()
            ->where('campaign_id', $campaign->id)
            ->where('status', PledgeStatus::Paid)
            ->with(['user', 'reward'])
            ->orderByDesc('paid_at')
            ->paginate($perPage);

        $stats = [
            'total_backers' => $campaign->pledges()->where('status', PledgeStatus::Paid)->count(),
            'total_raised' => (int) $campaign->pledged_amount,
            'progress' => (float) $campaign->calculateProgress(),
            'days_remaining' => (int) $campaign->daysRemaining(),
        ];

        return [
            'campaign' => $campaign,
            'stats' => $stats,
            'pledges' => $pledges,
        ];
    }
}
