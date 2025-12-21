<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\GetCampaignDashboardData;
use App\Actions\Dashboard\ListUserCampaigns;
use App\Domain\Campaign\Campaign;

class DashboardController extends Controller
{
    public function index(ListUserCampaigns $listUserCampaigns)
    {
        return view('spa');
    }

    public function show($id, GetCampaignDashboardData $getCampaignDashboardData)
    {
        return view('spa');
    }
}
