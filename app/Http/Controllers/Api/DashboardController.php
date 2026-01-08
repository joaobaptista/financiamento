<?php

namespace App\Http\Controllers\Api;

use App\Actions\Dashboard\GetCampaignDashboardData;
use App\Actions\Dashboard\ListUserCampaigns;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\PledgeResource;

class DashboardController
{
    public function index(ListUserCampaigns $listUserCampaigns)
    {
        $campaigns = $listUserCampaigns->execute(auth()->id());

        return CampaignResource::collection($campaigns);
    }

    public function show(int $id, GetCampaignDashboardData $getCampaignDashboardData)
    {
        $perPage = (int) request()->query('per_page', 25);
        $perPage = max(1, min($perPage, 100));

        $data = $getCampaignDashboardData->execute(auth()->id(), $id, $perPage);

        return [
            'campaign' => new CampaignResource($data['campaign']),
            'stats' => $data['stats'],
            'pledges' => PledgeResource::collection($data['pledges']),
        ];
    }

    public function exportExcel(int $id)
    {
        // Forçamos a resposta a ser CSV para evitar qualquer HTML residual
        if (ob_get_level()) ob_end_clean();

        // Suporte a autenticação via token na URL para downloads diretos
        if (request()->has('token')) {
            $userId = \Illuminate\Support\Facades\Cache::get('download_token_' . request()->query('token'));
            if ($userId) {
                auth()->loginUsingId($userId);
            }
        }

        if (!auth()->check()) {
            return response()->json(['message' => 'Não autorizado'], 401);
        }

        $campaign = \App\Domain\Campaign\Campaign::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pledges = \App\Domain\Pledge\Pledge::where('campaign_id', $campaign->id)
            ->where('status', \App\Enums\PledgeStatus::Paid->value)
            ->with(['user', 'reward'])
            ->get();

        $filename = "apoiadores_campanha_{$id}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function() use ($pledges) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($file, ['Nome', 'Email', 'Valor Total Pago', 'Valor da Recompensa', 'Valor do Frete', 'Data', 'Recompensa', 'CEP', 'Endereço', 'Número', 'Complemento', 'Bairro', 'Cidade', 'UF', 'Telefone']);

            foreach ($pledges as $p) {
                $amount = (int) ($p->amount ?? 0);
                $shippingAmount = (int) ($p->shipping_amount ?? 0);
                $totalAmount = $amount + $shippingAmount;

                fputcsv($file, [
                    $p->user?->name ?? 'N/A',
                    $p->user?->email ?? 'N/A',
                    number_format($totalAmount / 100, 2, ',', '.'),
                    number_format($amount / 100, 2, ',', '.'),
                    number_format($shippingAmount / 100, 2, ',', '.'),
                    $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : 'N/A',
                    $p->reward?->title ?? 'Apoio livre',
                    $p->user?->postal_code ?? '',
                    $p->user?->address_street ?? '',
                    $p->user?->address_number ?? '',
                    $p->user?->address_complement ?? '',
                    $p->user?->address_neighborhood ?? '',
                    $p->user?->address_city ?? '',
                    $p->user?->address_state ?? '',
                    $p->user?->phone ?? '',
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }

    public function generateDownloadToken()
    {
        $token = bin2hex(random_bytes(16));
        \Illuminate\Support\Facades\Cache::put('download_token_' . $token, auth()->id(), 60);

        // Retornamos um array simples que o Laravel converte para JSON
        return [
            'token' => $token
        ];
    }

    public function exportPdf(int $id)
    {
        // Limpamos qualquer saída anterior para garantir um documento limpo
        if (ob_get_level()) ob_end_clean();

        // Suporte a autenticação via token na URL
        if (request()->has('token')) {
            $userId = \Illuminate\Support\Facades\Cache::get('download_token_' . request()->query('token'));
            if ($userId) {
                auth()->loginUsingId($userId);
            }
        }

        // Verificamos se o usuário está autenticado
        if (!auth()->check()) {
            return response()->json(['message' => 'Não autorizado'], 401);
        }

        $campaign = \App\Domain\Campaign\Campaign::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pledges = \App\Domain\Pledge\Pledge::where('campaign_id', $campaign->id)
            ->where('status', \App\Enums\PledgeStatus::Paid->value)
            ->with(['user', 'reward'])
            ->orderBy('paid_at', 'asc')
            ->get();

        // Retornamos a view com cabeçalhos que forçam o navegador a tratar como um documento
        return response()
            ->view('exports.pledges_labels', [
                'campaign' => $campaign,
                'pledges' => $pledges
            ])
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
