<?php

namespace App\Http\Controllers\Api;

use App\Models\CreatorProfile;
use App\Models\CreatorPage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CreatorProfileController
{
    public function show(Request $request)
    {
        $user = $request->user();

        $profile = $user->creatorProfile;

        return response()->json([
            'profile' => $profile ? [
                'primary_category' => $profile->primary_category,
                'subcategory' => $profile->subcategory,
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'primary_category' => ['required', 'string', 'max:100'],
            'subcategory' => ['nullable', 'string', 'max:100'],
        ]);

        $profile = CreatorProfile::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'primary_category' => $validated['primary_category'],
                'subcategory' => $validated['subcategory'] ?? null,
            ]
        );

        $page = CreatorPage::ensureDefaultForUser($user);
        $page->update([
            'primary_category' => $profile->primary_category,
            'subcategory' => $profile->subcategory,
        ]);

        return response()->json([
            'ok' => true,
            'profile' => [
                'primary_category' => $profile->primary_category,
                'subcategory' => $profile->subcategory,
            ],
            'creator_page' => [
                'id' => $page->id,
                'slug' => $page->slug,
                'name' => $page->name,
            ],
        ]);
    }
}
