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
        if (!$this->resource) {
            return [];
        }

        return [
            'id' => $this->id,
            'amount' => (int) ($this->amount ?? 0),
            'status' => (string) ($this->status instanceof \BackedEnum ? $this->status->value : $this->status),
            'paid_at' => $this->paid_at?->toISOString(),
            'user' => $this->relationLoaded('user') && $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'reward' => $this->relationLoaded('reward') && $this->reward ? [
                'id' => $this->reward->id,
                'title' => $this->reward->title,
            ] : null,
        ];
    }
}
