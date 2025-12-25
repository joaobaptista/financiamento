<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *   @OA\Info(
 *     title="Origo API",
 *     version="1.0.0",
 *     description="API do Origo (Laravel)."
 *   ),
 *   @OA\Server(
 *     url="/",
 *     description="Default"
 *   )
 * )
 *
 * @OA\Tag(
 *   name="Pledges",
 *   description="Apoios e pagamentos"
 * )

 * @OA\Tag(
 *   name="Auth",
 *   description="Autenticação (sessão)"
 * )

 * @OA\Tag(
 *   name="Me",
 *   description="Endpoints do usuário autenticado"
 * )

 * @OA\Tag(
 *   name="Campaigns",
 *   description="Campanhas"
 * )
 *
 * @OA\Tag(
 *   name="Webhooks",
 *   description="Webhooks de provedores de pagamento"
 * )
 */
final class ApiDocumentation
{
    // This class exists only for Swagger/OpenAPI annotations scanning.
}
