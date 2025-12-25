<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *   path="/api/notifications",
 *   tags={"Notifications"},
 *   summary="Listar notificações in-app",
 *   @OA\Response(response=200, description="Lista", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=401, description="Não autenticado")
 * )
 *
 * @OA\Post(
 *   path="/api/notifications/{id}/read",
 *   tags={"Notifications"},
 *   summary="Marcar notificação como lida",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string"), example="1"),
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=401, description="Não autenticado"),
 *   @OA\Response(response=404, description="Não encontrado")
 * )
 *
 * @OA\Post(
 *   path="/api/notifications/read-all",
 *   tags={"Notifications"},
 *   summary="Marcar todas como lidas",
 *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=401, description="Não autenticado")
 * )
 */
final class NotificationsEndpoints
{
    // This class exists only for Swagger/OpenAPI annotations scanning.
}
