<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *   path="/api/webhooks/mercadopago",
 *   tags={"Webhooks"},
 *   summary="Webhook Mercado Pago",
 *   description="Endpoint chamado pelo Mercado Pago para notificar eventos de pagamento (ex.: payment.updated).",
 *   @OA\RequestBody(
 *     required=false,
 *     @OA\JsonContent(
 *       type="object",
 *       additionalProperties=true,
 *       example={"type":"payment","data":{"id":"123"}}
 *     )
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Aceito",
 *     @OA\JsonContent(
 *       @OA\Property(property="ok", type="boolean", example=true)
 *     )
 *   )
 * )
 */
final class WebhooksEndpoints
{
    // This class exists only for Swagger/OpenAPI annotations scanning.
}
