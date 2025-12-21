<?php

namespace App\Http\Controllers\Api;

use App\Actions\Campaign\CreateCampaign;
use App\Actions\Campaign\PublishCampaign;
use App\Actions\Campaign\UpdateCampaign;
use App\Data\Campaign\CreateCampaignData;
use App\Data\Campaign\RewardData;
use App\Data\Campaign\UpdateCampaignData;
use App\Domain\Campaign\Campaign;
use App\Enums\CampaignStatus;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Resources\CampaignResource;
use App\Services\Images\CampaignCoverImageService;
use App\Services\Money\Money;
use Illuminate\Support\Carbon;

class CampaignController
{
    public function show(int $id)
    {
        $campaign = Campaign::query()
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['user', 'rewards'])
            ->firstOrFail();

        return new CampaignResource($campaign);
    }

    public function store(StoreCampaignRequest $request, CreateCampaign $createCampaign, CampaignCoverImageService $coverImages)
    {
        $validated = $request->validated();

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

        $campaign = $createCampaign->execute($data)->loadMissing(['user', 'rewards']);

        if ($request->hasFile('cover_image')) {
            $coverImages->storeForCampaign($campaign, $request->file('cover_image'));
            $campaign->refresh()->loadMissing(['user', 'rewards']);
        }

        return (new CampaignResource($campaign))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateCampaignRequest $request, int $id, UpdateCampaign $updateCampaign, CampaignCoverImageService $coverImages)
    {
        $validated = $request->validated();

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
                campaignId: $id,
                userId: auth()->id(),
                title: $validated['title'],
                description: $validated['description'],
                goalAmount: Money::toCents($validated['goal_amount']),
                endsAt: Carbon::parse($validated['ends_at']),
                coverImagePath: $validated['cover_image_path'] ?? null,
                rewards: $rewards,
            );

            $campaign = $updateCampaign->execute($data)->loadMissing(['user', 'rewards']);

            if ($request->hasFile('cover_image')) {
                $coverImages->storeForCampaign($campaign, $request->file('cover_image'));
                $campaign->refresh()->loadMissing(['user', 'rewards']);
            }

            return new CampaignResource($campaign);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function publish(int $id, PublishCampaign $publishCampaign)
    {
        $campaign = Campaign::query()
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($campaign->status !== CampaignStatus::Draft) {
            return response()->json([
                'message' => 'Apenas campanhas em rascunho podem ser publicadas.',
            ], 422);
        }

        try {
            $publishCampaign->execute($campaign);

            return response()->json([
                'ok' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
