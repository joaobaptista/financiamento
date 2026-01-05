<?php

namespace App\Actions\Dashboard;

use App\Domain\Campaign\Campaign;
use Illuminate\Support\Collection;

class ListUserCampaigns
{
    /** @return Collection<int, Campaign> */
    public function execute(int $userId): Collection
    {
        return Campaign::query()
            ->where('user_id', $userId)
            ->withCount(['pledges' => function ($query) {
                $query->where('status', \App\Enums\PledgeStatus::Paid);
            }])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
