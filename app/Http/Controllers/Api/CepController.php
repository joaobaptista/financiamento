<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CepController
{
    public function show(Request $request, string $cep)
    {
        $cep = preg_replace('/\D+/', '', $cep) ?? '';

        if (!is_string($cep) || strlen($cep) !== 8) {
            return response()->json([
                'message' => 'CEP inválido.',
            ], 422);
        }

        $response = Http::acceptJson()
            ->timeout(5)
            ->get("https://viacep.com.br/ws/{$cep}/json/");

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Não foi possível buscar o CEP.',
            ], 422);
        }

        $data = (array) $response->json();

        if (($data['erro'] ?? false) === true) {
            return response()->json([
                'message' => 'CEP não encontrado.',
            ], 422);
        }

        return response()->json([
            'postal_code' => $data['cep'] ?? null,
            'address_street' => $data['logradouro'] ?? null,
            'address_neighborhood' => $data['bairro'] ?? null,
            'address_city' => $data['localidade'] ?? null,
            'address_state' => $data['uf'] ?? null,
        ]);
    }
}
