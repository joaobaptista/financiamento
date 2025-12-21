<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreatorPageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_user_id' => $this->owner_user_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'primary_category' => $this->primary_category,
            'subcategory' => $this->subcategory,
        ];
    }
}
