<?php

namespace App\Http\Controllers;

use App\Actions\Campaign\CreateCampaign;
use App\Actions\Campaign\PublishCampaign;
use App\Actions\Campaign\UpdateCampaign;
use App\Domain\Campaign\Campaign;
use App\Data\Campaign\CreateCampaignData;
use App\Data\Campaign\RewardData;
use App\Data\Campaign\UpdateCampaignData;
use App\Services\Money\Money;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::active()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('campaigns.index', compact('campaigns'));
    }

    public function show($slug)
    {
        $campaign = Campaign::where('slug', $slug)
            ->with(['user', 'rewards'])
            ->firstOrFail();

        return view('campaigns.show', compact('campaign'));
    }

    public function create()
    {
        return view('campaigns.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:1',
            'ends_at' => 'required|date|after:today',
            'cover_image_path' => 'nullable|string',
        ]);

        $rewards = [];
        if ($request->has('rewards')) {
            foreach ($request->rewards as $rewardData) {
                if (!empty($rewardData['title'])) {
                    $rewards[] = new RewardData(
                        title: $rewardData['title'],
                        description: $rewardData['description'] ?? '',
                        minAmount: Money::toCents($rewardData['min_amount'] ?? 0),
                        quantity: $rewardData['quantity'] ?? null,
                    );
                }
            }
        }

        $data = new CreateCampaignData(
            userId: auth()->id(),
            title: $validated['title'],
            description: $validated['description'],
            goalAmount: Money::toCents($validated['goal_amount']),
            endsAt: Carbon::parse($validated['ends_at']),
            coverImagePath: $validated['cover_image_path'] ?? null,
            rewards: $rewards,
        );

        (new CreateCampaign())->execute($data);

        return redirect()->route('dashboard.index')
            ->with('success', 'Campanha criada com sucesso!');
    }

    public function edit($id)
    {
        $campaign = Campaign::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('rewards')
            ->firstOrFail();

        if ($campaign->status !== 'draft') {
            return redirect()->route('dashboard.index')
                ->with('error', 'Apenas campanhas em rascunho podem ser editadas.');
        }

        return view('campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:1',
            'ends_at' => 'required|date|after:today',
            'cover_image_path' => 'nullable|string',
        ]);

        $rewards = [];
        if ($request->has('rewards')) {
            foreach ($request->rewards as $rewardData) {
                if (!empty($rewardData['title'])) {
                    $rewards[] = new RewardData(
                        title: $rewardData['title'],
                        description: $rewardData['description'] ?? '',
                        minAmount: Money::toCents($rewardData['min_amount'] ?? 0),
                        quantity: $rewardData['quantity'] ?? null,
                    );
                }
            }
        }

        try {
            $data = new UpdateCampaignData(
                campaignId: (int) $id,
                userId: auth()->id(),
                title: $validated['title'],
                description: $validated['description'],
                goalAmount: Money::toCents($validated['goal_amount']),
                endsAt: Carbon::parse($validated['ends_at']),
                coverImagePath: $validated['cover_image_path'] ?? null,
                rewards: $rewards,
            );

            (new UpdateCampaign())->execute($data);
        } catch (\RuntimeException $e) {
            return redirect()->route('dashboard.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('dashboard.index')
            ->with('success', 'Campanha atualizada com sucesso!');
    }

    public function publish($id)
    {
        $campaign = Campaign::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            $action = new PublishCampaign();
            $action->execute($campaign);

            return redirect()->route('dashboard.index')
                ->with('success', 'Campanha publicada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
