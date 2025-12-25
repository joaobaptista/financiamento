<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *   path="/api/dashboard/campaigns",
 *   tags={"Dashboard"},
 *   summary="Listar campanhas no dashboard do criador",
 *   @OA\Response(response=200, description="Lista", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=401, description="Não autenticado")
 * )
 *
 * @OA\Get(
 *   path="/api/dashboard/campaigns/{id}",
 *   tags={"Dashboard"},
 *   summary="Detalhe da campanha no dashboard",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
 *   @OA\Response(response=200, description="Detalhe", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=401, description="Não autenticado"),
 *   @OA\Response(response=404, description="Não encontrado")
 * )
 */
final class DashboardEndpoints
{
    // This class exists only for Swagger/OpenAPI annotations scanning.
}
