<?php

namespace App\Http\Controllers\Api;

use App\Domain\Campaign\Campaign;
use App\Http\Resources\CampaignResource;

class PublicCampaignController
{
    public function index()
    {
        $campaigns = Campaign::active()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(12);

        return CampaignResource::collection($campaigns);
    }

    public function show(string $slug)
    {
        $campaign = Campaign::query()
            ->where('slug', $slug)
            ->withCount([
                'pledges as supporters_count' => function ($query) {
                    $query->where('status', 'paid');
                },
            ])
            ->with(['user', 'rewards'])
            ->firstOrFail();

        return new CampaignResource($campaign);
    }
}
