<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentService;
use App\Data\Payments\PaymentResultData;
use App\Enums\PledgeStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MercadoPagoPaymentService implements PaymentService
{
    /** @var string */
    private string $baseUrl;

    /** @var string|null */
    private ?string $accessToken;

    public function __construct()
    {
        $this->baseUrl = (string) config('mercadopago.base_url', 'https://api.mercadopago.com');
        $this->accessToken = config('mercadopago.access_token');
    }

    public function processPayment(int $amount, string $paymentMethod, array $metadata = []): PaymentResultData
    {
        if (!is_string($this->accessToken) || $this->accessToken === '') {
            return new PaymentResultData(
                success: false,
                paymentId: null,
                amount: $amount,
                status: PledgeStatus::Canceled->value,
                paymentMethod: $paymentMethod,
                nextAction: null,
                message: 'Mercado Pago não configurado (MERCADOPAGO_ACCESS_TOKEN ausente).',
                raw: [],
            );
        }

        // IMPORTANT:
        // - Card payments require client-side tokenization with Mercado Pago SDK.
        // - For now we only prepare Pix via /v1/payments.
        if ($paymentMethod !== 'pix') {
            return new PaymentResultData(
                success: false,
                paymentId: null,
                amount: $amount,
                status: PledgeStatus::Canceled->value,
                paymentMethod: $paymentMethod,
                nextAction: null,
                message: 'Cartão requer tokenização via SDK do Mercado Pago (ainda não implementado).',
                raw: [],
            );
        }

        $transactionAmount = round($amount / 100, 2);

        $description = (string) ($metadata['description'] ?? 'Apoio');
        $payerEmail = (string) ($metadata['payer_email'] ?? 'payer@example.com');

        $externalReference = null;
        if (isset($metadata['pledge_id'])) {
            $externalReference = 'pledge_' . (string) $metadata['pledge_id'];
        }

        $payload = [
            'transaction_amount' => $transactionAmount,
            'description' => $description,
            'payment_method_id' => 'pix',
            'payer' => [
                'email' => $payerEmail,
            ],
        ];

        if ($externalReference) {
            $payload['external_reference'] = $externalReference;
        }

        // Allow Mercado Pago to call our webhook (configured in MP panel).
        $webhookUrl = (string) config('mercadopago.webhook_url');
        if ($webhookUrl !== '') {
            $payload['notification_url'] = $webhookUrl;
        }

        $idempotencyKey = (string) ($metadata['idempotency_key'] ?? Str::uuid()->toString());

        try {
            $response = Http::baseUrl($this->baseUrl)
                ->withToken($this->accessToken)
                ->acceptJson()
                ->asJson()
                ->withHeaders([
                    'X-Idempotency-Key' => $idempotencyKey,
                ])
                ->post('/v1/payments', $payload);

            if (!$response->successful()) {
                return new PaymentResultData(
                    success: false,
                    paymentId: null,
                    amount: $amount,
                    status: PledgeStatus::Canceled->value,
                    paymentMethod: 'pix',
                    nextAction: null,
                    message: 'Erro ao criar pagamento no Mercado Pago.',
                    raw: [
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ],
                );
            }

            $body = (array) $response->json();

            $mpPaymentId = isset($body['id']) ? (string) $body['id'] : null;
            $mpStatus = isset($body['status']) ? (string) $body['status'] : null;

            $status = $this->mapMercadoPagoStatusToPledgeStatus($mpStatus);

            $transactionData = $body['point_of_interaction']['transaction_data'] ?? null;
            $qrCode = is_array($transactionData) ? ($transactionData['qr_code'] ?? null) : null;
            $qrCodeBase64 = is_array($transactionData) ? ($transactionData['qr_code_base64'] ?? null) : null;

            $nextAction = null;
            if (is_string($qrCode) && $qrCode !== '') {
                $nextAction = [
                    'type' => 'pix',
                    'copy_paste' => $qrCode,
                    'qr_code_base64' => is_string($qrCodeBase64) ? $qrCodeBase64 : null,
                    'expires_at' => $body['date_of_expiration'] ?? null,
                ];
            }

            return new PaymentResultData(
                success: true,
                paymentId: $mpPaymentId,
                amount: $amount,
                status: $status,
                paymentMethod: 'pix',
                nextAction: $nextAction,
                message: null,
                raw: $body,
            );
        } catch (\Throwable $e) {
            return new PaymentResultData(
                success: false,
                paymentId: null,
                amount: $amount,
                status: PledgeStatus::Canceled->value,
                paymentMethod: 'pix',
                nextAction: null,
                message: 'Erro ao comunicar com Mercado Pago.',
                raw: [
                    'exception' => $e->getMessage(),
                ],
            );
        }
    }

    public function refundPayment(string $paymentId): PaymentResultData
    {
        if (!is_string($this->accessToken) || $this->accessToken === '') {
            return new PaymentResultData(
                success: false,
                paymentId: $paymentId,
                amount: 0,
                status: PledgeStatus::Refunded->value,
                message: 'Mercado Pago não configurado (MERCADOPAGO_ACCESS_TOKEN ausente).',
            );
        }

        // Mercado Pago refunds are handled via /v1/payments/{id}/refunds.
        try {
            $response = Http::baseUrl($this->baseUrl)
                ->withToken($this->accessToken)
                ->acceptJson()
                ->asJson()
                ->post("/v1/payments/{$paymentId}/refunds", []);

            if (!$response->successful()) {
                return new PaymentResultData(
                    success: false,
                    paymentId: $paymentId,
                    amount: 0,
                    status: PledgeStatus::Refunded->value,
                    message: 'Erro ao solicitar reembolso no Mercado Pago.',
                    raw: [
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ],
                );
            }

            return new PaymentResultData(
                success: true,
                paymentId: $paymentId,
                amount: 0,
                status: PledgeStatus::Refunded->value,
                message: 'Reembolso solicitado.',
                raw: (array) $response->json(),
            );
        } catch (\Throwable $e) {
            return new PaymentResultData(
                success: false,
                paymentId: $paymentId,
                amount: 0,
                status: PledgeStatus::Refunded->value,
                message: 'Erro ao comunicar com Mercado Pago.',
                raw: [
                    'exception' => $e->getMessage(),
                ],
            );
        }
    }

    private function mapMercadoPagoStatusToPledgeStatus(?string $mpStatus): string
    {
        return match ($mpStatus) {
            'approved' => PledgeStatus::Paid->value,
            'refunded', 'charged_back' => PledgeStatus::Refunded->value,
            'cancelled', 'rejected' => PledgeStatus::Canceled->value,
            default => PledgeStatus::Pending->value,
        };
    }
}
