<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *   path="/api/me/profile",
 *   tags={"Me"},
 *   summary="Exibir perfil do usuário",
 *   @OA\Response(response=200, description="Perfil", @OA\JsonContent(type="object", additionalProperties=true))
 * )
 *
 * @OA\Post(
 *   path="/api/me/profile",
 *   tags={"Me"},
 *   summary="Atualizar perfil do usuário",
 *   @OA\RequestBody(required=true, @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=200, description="Atualizado", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=422, description="Erro")
 * )
 *
 * @OA\Post(
 *   path="/api/me/password",
 *   tags={"Me"},
 *   summary="Atualizar senha",
 *   @OA\RequestBody(required=true, @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=200, description="Atualizada", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=422, description="Erro")
 * )
 *
 * @OA\Get(
 *   path="/api/me/supporter-profile",
 *   tags={"Me"},
 *   summary="Exibir perfil de apoiador (endereço/telefone)",
 *   @OA\Response(response=200, description="Perfil", @OA\JsonContent(type="object", additionalProperties=true))
 * )
 *
 * @OA\Post(
 *   path="/api/me/supporter-profile",
 *   tags={"Me"},
 *   summary="Atualizar perfil de apoiador (endereço/telefone)",
 *   @OA\RequestBody(required=true, @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=200, description="Atualizado", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=422, description="Erro")
 * )
 */
final class MeEndpoints
{
    // This class exists only for Swagger/OpenAPI annotations scanning.
}
