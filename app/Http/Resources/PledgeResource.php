<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PledgeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (int) $this->amount,
            'status' => (string) $this->status,
            'paid_at' => $this->paid_at?->toISOString(),
            'user' => new UserResource($this->whenLoaded('user')),
            'reward' => $this->whenLoaded('reward', function () {
                return [
                    'id' => $this->reward?->id,
                    'title' => $this->reward?->title,
                ];
            }),
        ];
    }
}
