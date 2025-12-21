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
        $perPage = (int) request()->query('per_page', 25);
        $perPage = max(1, min($perPage, 100));

        $data = $getCampaignDashboardData->execute(auth()->id(), $id, $perPage);

        return response()->json([
            'campaign' => new CampaignResource($data['campaign']),
            'stats' => $data['stats']->toArray(),
            'pledges' => PledgeResource::collection($data['pledges']),
        ]);
    }
}
