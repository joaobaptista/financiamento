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
        if (!$this->resource) {
            return [];
        }

        $status = $this->status;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'niche' => $this->niche,
            'goal_amount' => (int) ($this->goal_amount ?? 0),
            'pledged_amount' => (int) ($this->pledged_amount ?? 0),
            'starts_at' => $this->starts_at?->toISOString(),
            'ends_at' => $this->ends_at?->toISOString(),
            'status' => $status instanceof \BackedEnum ? $status->value : $status,
            'cover_image_path' => $this->cover_image_path,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
