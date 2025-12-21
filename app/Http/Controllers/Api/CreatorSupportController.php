<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

class CreatorSupportController
{
    public function show(Request $request, User $creator)
    {
        $supportersCount = $creator->supporters()->count();

        $isSupporting = false;
        $currentUser = $request->user();
        if ($currentUser) {
            $isSupporting = $currentUser
                ->supportedCreators()
                ->whereKey($creator->getKey())
                ->exists();
        }

        return response()->json([
            'creator_id' => $creator->id,
            'supporters_count' => $supportersCount,
            'is_supporting' => $isSupporting,
        ]);
    }

    public function store(Request $request, User $creator)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Você precisa estar autenticado.'], 401);
        }

        if ((int) $user->id === (int) $creator->id) {
            return response()->json(['message' => 'Você não pode apoiar a si mesmo.'], 422);
        }

        $user->supportedCreators()->syncWithoutDetaching([$creator->id]);

        return $this->show($request, $creator);
    }

    public function destroy(Request $request, User $creator)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Você precisa estar autenticado.'], 401);
        }

        $user->supportedCreators()->detach($creator->id);

        return $this->show($request, $creator);
    }
}
