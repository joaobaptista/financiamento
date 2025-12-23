<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UpdateMePasswordRequest;
use App\Http\Requests\UpdateMeProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MeProfileController
{
    public function show(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function update(UpdateMeProfileRequest $request)
    {
        $user = $request->user();

        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'postal_code' => $validated['postal_code'] ?? $user->postal_code,
            'address_street' => $validated['address_street'] ?? $user->address_street,
            'address_number' => $validated['address_number'] ?? $user->address_number,
            'address_complement' => $validated['address_complement'] ?? $user->address_complement,
            'address_neighborhood' => $validated['address_neighborhood'] ?? $user->address_neighborhood,
            'address_city' => $validated['address_city'] ?? $user->address_city,
            'address_state' => isset($validated['address_state']) ? Str::upper((string) $validated['address_state']) : $user->address_state,
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $file->storePublicly("avatars/{$user->id}", 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        return response()->json([
            'ok' => true,
            'user' => $user->fresh(),
        ]);
    }

    public function updatePassword(UpdateMePasswordRequest $request)
    {
        $user = $request->user();

        $validated = $request->validated();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'ok' => true,
        ]);
    }
}
