<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *   path="/api/cep/{cep}",
 *   tags={"CEP"},
 *   summary="Buscar endereço por CEP",
 *   @OA\Parameter(
 *     name="cep",
 *     in="path",
 *     required=true,
 *     description="CEP (apenas números ou com hífen)",
 *     @OA\Schema(type="string"),
 *     example="01311000"
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Endereço",
 *     @OA\JsonContent(type="object", additionalProperties=true)
 *   ),
 *   @OA\Response(response=404, description="CEP não encontrado"),
 *   @OA\Response(response=422, description="CEP inválido")
 * )
 */
final class CepEndpoints
{
    // This class exists only for Swagger/OpenAPI annotations scanning.
}
