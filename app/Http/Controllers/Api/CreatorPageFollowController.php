<?php

namespace App\Http\Controllers\Api;

use App\Models\CreatorPage;
use Illuminate\Http\Request;

class CreatorPageFollowController
{
    public function show(Request $request, CreatorPage $creatorPage)
    {
        $followersCount = $creatorPage->followers()->count();

        $isFollowing = false;
        $currentUser = $request->user();
        if ($currentUser) {
            $isFollowing = $currentUser
                ->followingCreatorPages()
                ->whereKey($creatorPage->getKey())
                ->exists();
        }

        return response()->json([
            'creator_page_id' => $creatorPage->id,
            'followers_count' => $followersCount,
            'is_following' => $isFollowing,
        ]);
    }

    public function store(Request $request, CreatorPage $creatorPage)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Você precisa estar autenticado.'], 401);
        }

        if ((int) $creatorPage->owner_user_id === (int) $user->id) {
            return response()->json(['message' => 'Você não pode seguir a sua própria página.'], 422);
        }

        $user->followingCreatorPages()->syncWithoutDetaching([$creatorPage->id]);

        return $this->show($request, $creatorPage);
    }

    public function destroy(Request $request, CreatorPage $creatorPage)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Você precisa estar autenticado.'], 401);
        }

        $user->followingCreatorPages()->detach($creatorPage->id);

        return $this->show($request, $creatorPage);
    }
}
