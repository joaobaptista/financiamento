<?php

namespace App\Http\Controllers\Webhooks;

use App\Actions\Pledge\ConfirmPayment;
use App\Domain\Pledge\Pledge;
use App\Enums\PledgeStatus;
use App\Notifications\PledgePaymentConfirmed;
use App\Notifications\PledgePaymentFailed;
use App\Notifications\PledgePaymentRefunded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController
{
    public function __invoke(Request $request, ConfirmPayment $confirmPayment)
    {
        $payload = $request->all();

        $paymentId = $payload['data']['id'] ?? ($payload['id'] ?? ($payload['data_id'] ?? null));
        if (is_int($paymentId) || is_float($paymentId)) {
            $paymentId = (string) $paymentId;
        }

        if (!is_string($paymentId) || trim($paymentId) === '') {
            Log::info('mercadopago.webhook.received', ['payload' => $payload]);
            return response()->json(['ok' => true]);
        }

        $paymentId = trim($paymentId);

        $accessToken = config('mercadopago.access_token');
        $baseUrl = (string) config('mercadopago.base_url', 'https://api.mercadopago.com' );
        if (!is_string($accessToken) || $accessToken === '') {
            Log::warning('mercadopago.webhook.no_token', ['payment_id' => $paymentId, 'payload' => $payload]);
            return response()->json(['ok' => true]);
        }

        // Tenta encontrar o apoio pelo ID do pagamento
        /** @var Pledge|null $pledge */
        $pledge = Pledge::query()->where('provider', 'mercadopago')->where('provider_payment_id', $paymentId)->first();

        // Se não encontrar pelo ID, busca os detalhes na API do Mercado Pago para usar a external_reference
        if (!$pledge) {
            try {
                $response = Http::baseUrl($baseUrl)
                    ->withToken($accessToken)
                    ->acceptJson()
                    ->get("/v1/payments/{$paymentId}");

                if ($response->successful()) {
                    $body = (array) $response->json();
                    $externalReference = (string) ($body['external_reference'] ?? '');
                    
                    if (str_starts_with($externalReference, 'pledge_')) {
                        $pledgeId = (int) str_replace('pledge_', '', $externalReference);
                        $pledge = Pledge::query()->find($pledgeId);
                        
                        // Se encontrou, vincula o ID do pagamento para processar a confirmação
                        if ($pledge && empty($pledge->provider_payment_id)) {
                            $pledge->provider_payment_id = $paymentId;
                            $pledge->save();
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error('mercadopago.webhook.fetch_error_during_lookup', ['payment_id' => $paymentId, 'error' => $e->getMessage()]);
            }
        }

        if (!$pledge) {
            Log::info('mercadopago.webhook.unknown_payment', ['payment_id' => $paymentId, 'payload' => $payload]);
            return response()->json(['ok' => true]);
        }

        try {
            // Re-consulta para garantir o status mais atualizado
            $response = Http::baseUrl($baseUrl)
                ->withToken($accessToken)
                ->acceptJson()
                ->get("/v1/payments/{$paymentId}");

            if (!$response->successful()) {
                Log::warning('mercadopago.webhook.fetch_failed', [
                    'payment_id' => $paymentId,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                return response()->json(['ok' => true]);
            }

            $body = (array) $response->json();
            $mpStatus = (string) ($body['status'] ?? '');

            $pledge->provider_payload = $body;
            $pledge->save();

            if ($mpStatus === 'approved') {
                $wasPaid = $pledge->status === PledgeStatus::Paid;
                // Esta Action atualiza o saldo da campanha automaticamente
                $confirmPayment->execute($pledge, $paymentId);

                $pledge->refresh();
                $pledge->loadMissing(['user']);
                if (!$wasPaid && $pledge->status === PledgeStatus::Paid && $pledge->user) {
                    $pledge->user->notify(new PledgePaymentConfirmed($pledge));
                }
                return response()->json(['ok' => true]);
            }

            if (in_array($mpStatus, ['cancelled', 'rejected'], true)) {
                $wasCanceled = $pledge->status === PledgeStatus::Canceled;
                $pledge->markAsCanceled();

                $pledge->refresh();
                $pledge->loadMissing(['user']);
                if (!$wasCanceled && $pledge->status === PledgeStatus::Canceled && $pledge->user) {
                    $reason = $mpStatus === 'rejected' ? 'Pagamento rejeitado' : 'Pagamento cancelado';
                    $pledge->user->notify(new PledgePaymentFailed($pledge, $reason));
                }
                return response()->json(['ok' => true]);
            }

            if (in_array($mpStatus, ['refunded', 'charged_back'], true)) {
                $wasRefunded = $pledge->status === PledgeStatus::Refunded;
                $pledge->markAsRefunded();

                $pledge->refresh();
                $pledge->loadMissing(['user']);
                if (!$wasRefunded && $pledge->status === PledgeStatus::Refunded && $pledge->user) {
                    $pledge->user->notify(new PledgePaymentRefunded($pledge));
                }
                return response()->json(['ok' => true]);
            }

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            Log::error('mercadopago.webhook.exception', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['ok' => true]);
        }
    }
}
