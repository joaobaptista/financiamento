<?php

namespace App\Http\Controllers\Api;

use App\Domain\Campaign\Campaign;
use App\Http\Resources\CampaignResource;
use Illuminate\Http\Request;

class PublicCampaignController
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));

        $campaignsQuery = Campaign::active()
            ->with(['user', 'creatorPage'])
            ->orderByDesc('created_at');

        if ($q !== '') {
            $campaignsQuery->where(function ($builder) use ($q) {
                $builder
                    ->where('title', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%');
            });
        }

        if ($category !== '') {
            $campaignsQuery->where('niche', $category);
        }

        $campaigns = $campaignsQuery->paginate(12);

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
            ->with(['user', 'rewards.fretes', 'creatorPage'])
            ->firstOrFail();

        return new CampaignResource($campaign);
    }
}
