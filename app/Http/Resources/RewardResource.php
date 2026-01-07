<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fretes = [];
        if ($this->relationLoaded('fretes')) {
            foreach ($this->fretes as $frete) {
                $fretes[$frete->regiao] = (int) $frete->valor;
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'min_amount' => (int) $this->min_amount,
            'quantity' => $this->quantity,
            'remaining' => $this->remaining,
            'fretes' => !empty($fretes) ? $fretes : null,
        ];
    }
}
