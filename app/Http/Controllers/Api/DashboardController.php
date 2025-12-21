<?php

namespace App\Http\Controllers\Api;

use App\Actions\Dashboard\GetCampaignDashboardData;
use App\Actions\Dashboard\ListUserCampaigns;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\PledgeResource;

class DashboardController
{
    public function index(ListUserCampaigns $listUserCampaigns)
    {
        $campaigns = $listUserCampaigns->execute(auth()->id());

        return CampaignResource::collection($campaigns);
    }

    public function show(int $id, GetCampaignDashboardData $getCampaignDashboardData)
    {
        $data = $getCampaignDashboardData->execute(auth()->id(), $id);

        return response()->json([
            'campaign' => new CampaignResource($data['campaign']->loadMissing(['user', 'rewards'])),
            'stats' => $data['stats']->toArray(),
            'pledges' => PledgeResource::collection($data['campaign']->pledges),
        ]);
    }
}
