<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *   path="/api/pledges",
 *   tags={"Pledges"},
 *   summary="Criar um apoio (pledge) e iniciar o pagamento",
 *   description="Cria um pledge e inicia pagamento via driver configurado (mock ou Mercado Pago).",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"campaign_id","amount","payment_method"},
 *       @OA\Property(property="campaign_id", type="integer", example=1),
 *       @OA\Property(property="amount", type="string", example="10.00", description="Valor em reais (string/decimal)"),
 *       @OA\Property(property="payment_method", type="string", enum={"pix","card"}, example="pix"),
 *       @OA\Property(property="reward_id", type="integer", nullable=true, example=null),
 *
 *       @OA\Property(property="card_token", type="string", nullable=true, example="tok_test_123", description="Obrigatório para card em modo Mercado Pago"),
 *       @OA\Property(property="installments", type="integer", nullable=true, example=1),
 *       @OA\Property(property="payment_method_id", type="string", nullable=true, example="visa"),
 *       @OA\Property(property="payer_identification_type", type="string", nullable=true, enum={"CPF","CNPJ"}, example="CPF"),
 *       @OA\Property(property="payer_identification_number", type="string", nullable=true, example="12345678901")
 *     )
 *   ),
 *   @OA\Response(
 *     response=201,
 *     description="Pledge criado",
 *     @OA\JsonContent(
 *       @OA\Property(property="ok", type="boolean", example=true),
 *       @OA\Property(property="pledge_id", type="integer", example=123),
 *       @OA\Property(property="status", type="string", enum={"pending","paid","canceled","refunded"}, example="pending"),
 *       @OA\Property(
 *         property="payment",
 *         type="object",
 *         @OA\Property(property="method", type="string", enum={"pix","card"}, example="pix"),
 *         @OA\Property(property="status", type="string", enum={"pending","paid","canceled","refunded"}, example="pending"),
 *         @OA\Property(property="payment_id", type="string", nullable=true, example="123456"),
 *         @OA\Property(
 *           property="next_action",
 *           type="object",
 *           nullable=true,
 *           @OA\Property(property="type", type="string", example="pix"),
 *           @OA\Property(property="copy_paste", type="string", nullable=true),
 *           @OA\Property(property="qr_code_base64", type="string", nullable=true),
 *           @OA\Property(property="expires_at", type="string", nullable=true, example="2025-12-25T12:34:56Z"),
 *           @OA\Property(property="confirmable", type="boolean", nullable=true, example=false)
 *         )
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=422,
 *     description="Erro de validação ou processamento do pagamento",
 *     @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Erro ao processar pagamento. Tente novamente."),
 *       @OA\Property(property="provider", type="object", nullable=true, description="Apenas quando APP_DEBUG=true")
 *     )
 *   )
 * )
 *
 * @OA\Get(
 *   path="/api/pledges/{id}",
 *   tags={"Pledges"},
 *   summary="Consultar status do pledge",
 *   description="Usado pela SPA para polling do Pix (confirmação via webhook).",
 *   @OA\Parameter(
 *     name="id",
 *     in="path",
 *     required=true,
 *     @OA\Schema(type="integer"),
 *     example=123
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Status atual",
 *     @OA\JsonContent(
 *       @OA\Property(property="ok", type="boolean", example=true),
 *       @OA\Property(property="pledge_id", type="integer", example=123),
 *       @OA\Property(property="status", type="string", enum={"pending","paid","canceled","refunded"}, example="pending"),
 *       @OA\Property(
 *         property="payment",
 *         type="object",
 *         @OA\Property(property="method", type="string", enum={"pix","card"}, example="pix"),
 *         @OA\Property(property="provider_payment_id", type="string", nullable=true, example="123456")
 *       )
 *     )
 *   ),
 *   @OA\Response(response=404, description="Não encontrado")
 * )
 *
 * @OA\Post(
 *   path="/api/pledges/{id}/confirm",
 *   tags={"Pledges"},
 *   summary="Confirmar Pix manualmente (apenas mock)",
 *   description="No driver Mercado Pago a confirmação é automática (webhook).",
 *   @OA\Parameter(
 *     name="id",
 *     in="path",
 *     required=true,
 *     @OA\Schema(type="integer"),
 *     example=123
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Confirmado",
 *     @OA\JsonContent(
 *       @OA\Property(property="ok", type="boolean", example=true),
 *       @OA\Property(property="status", type="string", example="paid")
 *     )
 *   ),
 *   @OA\Response(
 *     response=422,
 *     description="Não permitido / inválido",
 *     @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="No Mercado Pago, a confirmação é automática. Aguarde ou atualize a página.")
 *     )
 *   )
 * )
 */
final class PledgesEndpoints
{
    // This class exists only for Swagger/OpenAPI annotations scanning.
}
