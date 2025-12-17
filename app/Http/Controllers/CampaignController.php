<?php

namespace App\Http\Controllers;

use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Reward;
use App\Actions\PublishCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        // Converter goal_amount para centavos
        $validated['goal_amount'] = $validated['goal_amount'] * 100;
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']);

        $campaign = Campaign::create($validated);

        // Processar rewards se enviados
        if ($request->has('rewards')) {
            foreach ($request->rewards as $rewardData) {
                if (!empty($rewardData['title'])) {
                    Reward::create([
                        'campaign_id' => $campaign->id,
                        'title' => $rewardData['title'],
                        'description' => $rewardData['description'] ?? '',
                        'min_amount' => ($rewardData['min_amount'] ?? 0) * 100,
                        'quantity' => $rewardData['quantity'] ?? null,
                        'remaining' => $rewardData['quantity'] ?? null,
                    ]);
                }
            }
        }

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
        $campaign = Campaign::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($campaign->status !== 'draft') {
            return redirect()->route('dashboard.index')
                ->with('error', 'Apenas campanhas em rascunho podem ser editadas.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:1',
            'ends_at' => 'required|date|after:today',
            'cover_image_path' => 'nullable|string',
        ]);

        $validated['goal_amount'] = $validated['goal_amount'] * 100;
        $validated['slug'] = Str::slug($validated['title']);

        $campaign->update($validated);

        // Atualizar rewards
        if ($request->has('rewards')) {
            // Deletar rewards existentes
            $campaign->rewards()->delete();

            // Criar novos
            foreach ($request->rewards as $rewardData) {
                if (!empty($rewardData['title'])) {
                    Reward::create([
                        'campaign_id' => $campaign->id,
                        'title' => $rewardData['title'],
                        'description' => $rewardData['description'] ?? '',
                        'min_amount' => ($rewardData['min_amount'] ?? 0) * 100,
                        'quantity' => $rewardData['quantity'] ?? null,
                        'remaining' => $rewardData['quantity'] ?? null,
                    ]);
                }
            }
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
