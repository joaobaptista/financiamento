<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *   path="/api/creators/{creator}/support",
 *   tags={"Creators"},
 *   summary="Ver status de apoio a um criador",
 *   @OA\Parameter(name="creator", in="path", required=true, @OA\Schema(type="string"), example="criador-teste"),
 *   @OA\Response(response=200, description="Status", @OA\JsonContent(type="object", additionalProperties=true))
 * )
 *
 * @OA\Post(
 *   path="/api/creators/{creator}/support",
 *   tags={"Creators"},
 *   summary="Apoiar/seguir criador (autenticado)",
 *   @OA\Parameter(name="creator", in="path", required=true, @OA\Schema(type="string"), example="criador-teste"),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=401, description="Não autenticado")
 * )
 *
 * @OA\Delete(
 *   path="/api/creators/{creator}/support",
 *   tags={"Creators"},
 *   summary="Deixar de apoiar/seguir criador (autenticado)",
 *   @OA\Parameter(name="creator", in="path", required=true, @OA\Schema(type="string"), example="criador-teste"),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=401, description="Não autenticado")
 * )
 *
 * @OA\Get(
 *   path="/api/creator-pages/{creatorPage}/follow",
 *   tags={"Creators"},
 *   summary="Ver status de follow na creator page",
 *   @OA\Parameter(name="creatorPage", in="path", required=true, @OA\Schema(type="string"), example="criador-teste"),
 *   @OA\Response(response=200, description="Status", @OA\JsonContent(type="object", additionalProperties=true))
 * )
 *
 * @OA\Post(
 *   path="/api/creator-pages/{creatorPage}/follow",
 *   tags={"Creators"},
 *   summary="Seguir creator page (autenticado)",
 *   @OA\Parameter(name="creatorPage", in="path", required=true, @OA\Schema(type="string"), example="criador-teste"),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=401, description="Não autenticado")
 * )
 *
 * @OA\Delete(
 *   path="/api/creator-pages/{creatorPage}/follow",
 *   tags={"Creators"},
 *   summary="Deixar de seguir creator page (autenticado)",
 *   @OA\Parameter(name="creatorPage", in="path", required=true, @OA\Schema(type="string"), example="criador-teste"),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=401, description="Não autenticado")
 * )
 */
final class CreatorsEndpoints
{
    // This class exists only for Swagger/OpenAPI annotations scanning.
}
