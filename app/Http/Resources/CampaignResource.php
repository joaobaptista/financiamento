<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->status;

        $coverImagePath = $this->normalizeCoverPath($this->cover_image_path);
        $coverImageWebpPath = $this->normalizeCoverPath($this->cover_image_webp_path);

        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'creator_page' => new CreatorPageResource($this->whenLoaded('creatorPage')),
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'goal_amount' => (int) $this->goal_amount,
            'pledged_amount' => (int) $this->pledged_amount,
            'pledges_count' => $this->when(isset($this->pledges_count), (int) $this->pledges_count),
            'supporters_count' => $this->when(isset($this->supporters_count), (int) $this->supporters_count),
            'starts_at' => $this->starts_at?->toISOString(),
            'ends_at' => $this->ends_at?->toISOString(),
            'status' => $status instanceof \BackedEnum ? $status->value : $status,
            'cover_image_path' => $coverImagePath,
            'cover_image_webp_path' => $coverImageWebpPath,
            'created_at' => $this->created_at?->toISOString(),
            'rewards' => RewardResource::collection($this->whenLoaded('rewards')),
        ];
    }

    private function normalizeCoverPath($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        if (preg_match('#^[a-z][a-z0-9+\-.]*://#i', $raw) === 1 || str_starts_with($raw, 'data:')) {
            return $raw;
        }

        if (str_starts_with($raw, '/storage/') || str_starts_with($raw, 'storage/')) {
            return str_starts_with($raw, '/') ? $raw : '/' . $raw;
        }

        if (!str_starts_with($raw, '/')) {
            return '/storage/' . ltrim($raw, '/');
        }

        return $raw;
    }
}
