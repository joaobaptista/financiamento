<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *   path="/api/me",
 *   tags={"Auth"},
 *   summary="Retorna o usuário autenticado (ou null)",
 *   description="Usa sessão (cookies). Em caso de não autenticado, o backend geralmente retorna ok com user=null.",
 *   @OA\Response(
 *     response=200,
 *     description="Estado atual da sessão",
 *     @OA\JsonContent(
 *       type="object",
 *       additionalProperties=true
 *     )
 *   )
 * )
 *
 * @OA\Post(
 *   path="/api/login",
 *   tags={"Auth"},
 *   summary="Login (sessão)",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"email","password"},
 *       @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *       @OA\Property(property="password", type="string", format="password", example="secret" )
 *     )
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Login ok",
 *     @OA\JsonContent(type="object", additionalProperties=true)
 *   ),
 *   @OA\Response(response=422, description="Erro de validação")
 * )
 *
 * @OA\Post(
 *   path="/api/register",
 *   tags={"Auth"},
 *   summary="Cadastro (sessão)",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"name","email","password"},
 *       @OA\Property(property="name", type="string", example="Usuário"),
 *       @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *       @OA\Property(property="password", type="string", format="password", example="secret" )
 *     )
 *   ),
 *   @OA\Response(
 *     response=201,
 *     description="Cadastro ok",
 *     @OA\JsonContent(type="object", additionalProperties=true)
 *   ),
 *   @OA\Response(response=422, description="Erro de validação")
 * )
 *
 * @OA\Post(
 *   path="/api/logout",
 *   tags={"Auth"},
 *   summary="Logout (encerra sessão)",
 *   @OA\Response(
 *     response=200,
 *     description="Logout ok",
 *     @OA\JsonContent(type="object", additionalProperties=true)
 *   )
 * )
 */
final class AuthEndpoints
{
    // This class exists only for Swagger/OpenAPI annotations scanning.
}
