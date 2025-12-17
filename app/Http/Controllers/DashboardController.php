<?php

namespace App\Http\Controllers;

use App\Domain\Campaign\Campaign;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::where('user_id', auth()->id())
            ->withCount('pledges')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.index', compact('campaigns'));
    }

    public function show($id)
    {
        $campaign = Campaign::where('id', $id)
            ->where('user_id', auth()->id())
            ->with([
                'pledges.user',
                'pledges' => function ($query) {
                    $query->where('status', 'paid')->orderBy('paid_at', 'desc');
                }
            ])
            ->firstOrFail();

        $stats = [
            'total_backers' => $campaign->pledges()->where('status', 'paid')->count(),
            'total_raised' => $campaign->pledged_amount,
            'progress' => $campaign->calculateProgress(),
            'days_remaining' => $campaign->daysRemaining(),
        ];

        return view('dashboard.show', compact('campaign', 'stats'));
    }
}
