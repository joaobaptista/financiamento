<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *   path="/api/campaigns",
 *   tags={"Campaigns"},
 *   summary="Listar campanhas públicas",
 *   @OA\Response(
 *     response=200,
 *     description="Lista",
 *     @OA\JsonContent(type="object", additionalProperties=true)
 *   )
 * )
 *
 * @OA\Get(
 *   path="/api/campaigns/{slug}",
 *   tags={"Campaigns"},
 *   summary="Detalhe de campanha pública",
 *   @OA\Parameter(
 *     name="slug",
 *     in="path",
 *     required=true,
 *     @OA\Schema(type="string"),
 *     example="campanha-exemplo"
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Campanha",
 *     @OA\JsonContent(type="object", additionalProperties=true)
 *   ),
 *   @OA\Response(response=404, description="Não encontrado")
 * )
 *
 * @OA\Post(
 *   path="/api/me/campaigns",
 *   tags={"Campaigns"},
 *   summary="Criar campanha (autenticado)",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"title","description","goal_amount","ends_at"},
 *       @OA\Property(property="title", type="string", example="Minha campanha"),
 *       @OA\Property(property="description", type="string", example="Descrição"),
 *       @OA\Property(property="goal_amount", type="string", example="1000.00"),
 *       @OA\Property(property="ends_at", type="string", example="2026-01-31")
 *     )
 *   ),
 *   @OA\Response(response=201, description="Criada", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=422, description="Erro")
 * )
 *
 * @OA\Get(
 *   path="/api/me/campaigns/{id}",
 *   tags={"Campaigns"},
 *   summary="Ver campanha do criador (autenticado)",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
 *   @OA\Response(response=200, description="Campanha", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=404, description="Não encontrado")
 * )
 *
 * @OA\Put(
 *   path="/api/me/campaigns/{id}",
 *   tags={"Campaigns"},
 *   summary="Atualizar campanha (autenticado)",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
 *   @OA\RequestBody(required=true, @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=200, description="Atualizada", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=422, description="Erro")
 * )
 *
 * @OA\Delete(
 *   path="/api/me/campaigns/{id}",
 *   tags={"Campaigns"},
 *   summary="Excluir campanha (autenticado)",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
 *   @OA\Response(response=200, description="Excluída", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=404, description="Não encontrado")
 * )
 *
 * @OA\Post(
 *   path="/api/me/campaigns/{id}/publish",
 *   tags={"Campaigns"},
 *   summary="Publicar campanha (autenticado)",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
 *   @OA\Response(response=200, description="Publicada", @OA\JsonContent(type="object", additionalProperties=true)),
 *   @OA\Response(response=422, description="Erro")
 * )
 */
final class CampaignsEndpoints
{
    // This class exists only for Swagger/OpenAPI annotations scanning.
}
