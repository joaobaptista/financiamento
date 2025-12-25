<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'niche' => 'required|string|max:50',
            'goal_amount' => 'required|numeric|min:1',
            'ends_at' => 'required|date|after:today',
            'cover_image_path' => 'nullable|string',
            'cover_image' => 'nullable|file|image|max:5120',
            'rewards' => 'nullable|array',
            'rewards.*.title' => 'nullable|string',
            'rewards.*.description' => 'nullable|string',
            'rewards.*.min_amount' => 'nullable|numeric|min:0',
            'rewards.*.quantity' => 'nullable|integer|min:1',
        ];
    }
}
