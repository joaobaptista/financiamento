<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\GetCampaignDashboardData;
use App\Actions\Dashboard\ListUserCampaigns;
use App\Domain\Campaign\Campaign;

class DashboardController extends Controller
{
    public function index(ListUserCampaigns $listUserCampaigns)
    {
        $campaigns = $listUserCampaigns->execute(auth()->id());

        return view('dashboard.index', compact('campaigns'));
    }

    public function show($id, GetCampaignDashboardData $getCampaignDashboardData)
    {
        $data = $getCampaignDashboardData->execute(auth()->id(), (int) $id);
        $campaign = $data['campaign'];
        $stats = $data['stats']->toArray();

        return view('dashboard.show', compact('campaign', 'stats'));
    }
}
