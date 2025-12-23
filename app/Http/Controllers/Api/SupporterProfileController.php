<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UpdateSupporterProfileRequest;
use Illuminate\Http\Request;

class SupporterProfileController
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'postal_code' => $user?->postal_code,
            'address_street' => $user?->address_street,
            'address_number' => $user?->address_number,
            'address_complement' => $user?->address_complement,
            'address_neighborhood' => $user?->address_neighborhood,
            'address_city' => $user?->address_city,
            'address_state' => $user?->address_state,
            'phone' => $user?->phone,
        ]);
    }

    public function update(UpdateSupporterProfileRequest $request)
    {
        $user = $request->user();

        $user->update($request->validated());

        return response()->json([
            'ok' => true,
        ]);
    }
}
