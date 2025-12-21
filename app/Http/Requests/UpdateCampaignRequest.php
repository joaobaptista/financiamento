<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignRequest extends FormRequest
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
            'goal_amount' => 'required|numeric|min:1',
            'ends_at' => 'required|date|after:today',
            'cover_image_path' => 'nullable|string',
            'rewards' => 'nullable|array',
            'rewards.*.title' => 'nullable|string',
            'rewards.*.description' => 'nullable|string',
            'rewards.*.min_amount' => 'nullable|numeric|min:0',
            'rewards.*.quantity' => 'nullable|integer|min:1',
        ];
    }
}
