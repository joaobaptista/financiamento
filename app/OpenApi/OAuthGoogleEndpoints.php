<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *   path="/api/oauth/google/redirect",
 *   tags={"OAuth"},
 *   summary="Iniciar login com Google",
 *   description="Retorna/efetua o redirect para o provedor Google",
 *   @OA\Response(response=302, description="Redirect")
 * )
 *
 * @OA\Get(
 *   path="/api/oauth/google/callback",
 *   tags={"OAuth"},
 *   summary="Callback do Google OAuth",
 *   description="Callback do provedor Google; finaliza autenticação",
 *   @OA\Parameter(name="code", in="query", required=false, @OA\Schema(type="string")),
 *   @OA\Parameter(name="state", in="query", required=false, @OA\Schema(type="string")),
 *   @OA\Response(response=302, description="Redirect")
 * )
 */
final class OAuthGoogleEndpoints
{
    // This class exists only for Swagger/OpenAPI annotations scanning.
}
