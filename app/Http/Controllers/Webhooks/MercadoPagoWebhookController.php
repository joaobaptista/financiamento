<?php

namespace App\Http\Controllers\Webhooks;

use App\Actions\Pledge\ConfirmPayment;
use App\Domain\Pledge\Pledge;
use App\Enums\PledgeStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController
{
    public function __invoke(Request $request, ConfirmPayment $confirmPayment)
    {
        // MP payloads vary by topic. Common patterns include:
        // - {"type":"payment","data":{"id":"123"}}
        // - {"action":"payment.updated","data":{"id":"123"}}
        $payload = $request->all();

        $paymentId = $payload['data']['id'] ?? ($payload['id'] ?? null);
        if (!is_string($paymentId) || $paymentId === '') {
            Log::info('mercadopago.webhook.received', ['payload' => $payload]);
            return response()->json(['ok' => true]);
        }

        // If we can't query MP (no token), just ack and keep payload for later debugging.
        $accessToken = config('mercadopago.access_token');
        $baseUrl = (string) config('mercadopago.base_url', 'https://api.mercadopago.com');
        if (!is_string($accessToken) || $accessToken === '') {
            Log::warning('mercadopago.webhook.no_token', ['payment_id' => $paymentId, 'payload' => $payload]);
            return response()->json(['ok' => true]);
        }

        // Find our pledge by provider payment id.
        /** @var Pledge|null $pledge */
        $pledge = Pledge::query()->where('provider', 'mercadopago')->where('provider_payment_id', $paymentId)->first();
        if (!$pledge) {
            Log::info('mercadopago.webhook.unknown_payment', ['payment_id' => $paymentId]);
            return response()->json(['ok' => true]);
        }

        try {
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

            // Store latest provider payload for auditing.
            $pledge->provider_payload = $body;
            $pledge->save();

            if ($mpStatus === 'approved') {
                $confirmPayment->execute($pledge, $paymentId);
                return response()->json(['ok' => true]);
            }

            if (in_array($mpStatus, ['cancelled', 'rejected'], true)) {
                $pledge->markAsCanceled();
                return response()->json(['ok' => true]);
            }

            if (in_array($mpStatus, ['refunded', 'charged_back'], true)) {
                $pledge->markAsRefunded();
                return response()->json(['ok' => true]);
            }

            // pending / in_process etc -> keep pending
            if ($pledge->status === PledgeStatus::Paid) {
                return response()->json(['ok' => true]);
            }

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            Log::error('mercadopago.webhook.exception', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            // Always ACK to avoid repeated deliveries during development.
            return response()->json(['ok' => true]);
        }
    }
}
