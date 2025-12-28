<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentService;
use App\Data\Payments\PaymentResultData;
use App\Enums\PledgeStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        $transactionAmount = round($amount / 100, 2);

        $description = (string) ($metadata['description'] ?? 'Apoio');
        $payerEmail = $this->resolvePayerEmail($metadata);
        $currencyId = (string) config('mercadopago.currency', 'BRL');

        $externalReference = null;
        if (isset($metadata['pledge_id'])) {
            $externalReference = 'pledge_' . (string) $metadata['pledge_id'];
        }

        $idempotencyKey = (string) ($metadata['idempotency_key'] ?? Str::uuid()->toString());

        $payload = match ($paymentMethod) {
            'pix' => $this->buildPixPayload(
                transactionAmount: $transactionAmount,
                description: $description,
                payerEmail: $payerEmail,
                externalReference: $externalReference,
                currencyId: $currencyId,
            ),
            'card' => $this->buildCardPayload(
                transactionAmount: $transactionAmount,
                description: $description,
                payerEmail: $payerEmail,
                externalReference: $externalReference,
                metadata: $metadata,
                currencyId: $currencyId,
            ),
            default => null,
        };

        if (!is_array($payload)) {
            return new PaymentResultData(
                success: false,
                paymentId: null,
                amount: $amount,
                status: PledgeStatus::Canceled->value,
                paymentMethod: $paymentMethod,
                nextAction: null,
                message: 'Forma de pagamento inválida.',
                raw: [],
            );
        }

        // Allow Mercado Pago to call our webhook (configured in MP panel).
        $webhookUrl = (string) config('mercadopago.webhook_url');
        if ($webhookUrl !== '') {
            $payload['notification_url'] = $webhookUrl;
        }

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
                Log::warning('mercadopago.payments.create_failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'payment_method' => $paymentMethod,
                    'external_reference' => $externalReference,
                    'idempotency_key' => $idempotencyKey,
                    'payload' => $this->redactPayload($payload),
                ]);

                return new PaymentResultData(
                    success: false,
                    paymentId: null,
                    amount: $amount,
                    status: PledgeStatus::Canceled->value,
                    paymentMethod: $paymentMethod,
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

            if ($status === PledgeStatus::Canceled->value) {
                return new PaymentResultData(
                    success: false,
                    paymentId: $mpPaymentId,
                    amount: $amount,
                    status: $status,
                    paymentMethod: $paymentMethod,
                    nextAction: null,
                    message: 'Pagamento recusado.',
                    raw: $body,
                );
            }

            $nextAction = null;
            if ($paymentMethod === 'pix') {
                $transactionData = $body['point_of_interaction']['transaction_data'] ?? null;
                $qrCode = is_array($transactionData) ? ($transactionData['qr_code'] ?? null) : null;
                $qrCodeBase64 = is_array($transactionData) ? ($transactionData['qr_code_base64'] ?? null) : null;

                if (is_string($qrCode) && $qrCode !== '') {
                    $nextAction = [
                        'type' => 'pix',
                        'copy_paste' => $qrCode,
                        'qr_code_base64' => is_string($qrCodeBase64) ? $qrCodeBase64 : null,
                        'expires_at' => $body['date_of_expiration'] ?? null,
                        'confirmable' => false,
                    ];
                }
            }

            return new PaymentResultData(
                success: true,
                paymentId: $mpPaymentId,
                amount: $amount,
                status: $status,
                paymentMethod: $paymentMethod,
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
                paymentMethod: $paymentMethod,
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

    private function buildPixPayload(
        float $transactionAmount,
        string $description,
        string $payerEmail,
        ?string $externalReference,
        string $currencyId,
    ): array {
        $payload = [
            'transaction_amount' => $transactionAmount,
            'currency_id' => $currencyId,
            'description' => $description,
            'payment_method_id' => 'pix',
            'payer' => [
                'email' => $payerEmail,
            ],
        ];

        if (is_string($externalReference) && $externalReference !== '') {
            $payload['external_reference'] = $externalReference;
        }

        return $payload;
    }

    private function buildCardPayload(
        float $transactionAmount,
        string $description,
        string $payerEmail,
        ?string $externalReference,
        array $metadata,
        string $currencyId,
    ): array {
        $token = (string) ($metadata['card_token'] ?? '');
        $installments = (int) ($metadata['installments'] ?? 1);
        $paymentMethodId = $metadata['payment_method_id'] ?? null;
        $issuerId = $metadata['issuer_id'] ?? null;
        $payerIdentificationType = $metadata['payer_identification_type'] ?? null;
        $payerIdentificationNumber = $metadata['payer_identification_number'] ?? null;

        if ($token === '') {
            throw new \InvalidArgumentException('Token de cartão ausente.');
        }

        $payload = [
            'transaction_amount' => $transactionAmount,
            'currency_id' => $currencyId,
            'description' => $description,
            'token' => $token,
            'installments' => max(1, min(12, $installments)),
            'payer' => [
                'email' => $payerEmail,
            ],
        ];

        if (is_string($externalReference) && $externalReference !== '') {
            $payload['external_reference'] = $externalReference;
        }

        if (is_string($paymentMethodId) && $paymentMethodId !== '') {
            $payload['payment_method_id'] = $paymentMethodId;
        }

        if (is_string($issuerId) && $issuerId !== '') {
            $payload['issuer_id'] = $issuerId;
        }

        if (
            is_string($payerIdentificationType) && $payerIdentificationType !== '' &&
            is_string($payerIdentificationNumber) && $payerIdentificationNumber !== ''
        ) {
            $payload['payer']['identification'] = [
                'type' => $payerIdentificationType,
                'number' => $payerIdentificationNumber,
            ];
        }

        return $payload;
    }

    private function resolvePayerEmail(array $metadata): string
    {
        $payerEmail = '';
        if (isset($metadata['payer_email']) && is_string($metadata['payer_email'])) {
            $payerEmail = trim($metadata['payer_email']);
        }

        if ($payerEmail === '') {
            $payerEmail = 'payer@example.com';
        }

        // Mercado Pago can require a test payer email when using TEST credentials.
        $accessToken = is_string($this->accessToken) ? $this->accessToken : '';
        $isTestToken = Str::startsWith($accessToken, ['TEST-', 'TEST_', 'TEST']);
        if ($isTestToken && !Str::endsWith($payerEmail, '@testuser.com')) {
            $payerEmail = (string) config('mercadopago.test_payer_email', 'test_user_123@testuser.com');
        }

        return $payerEmail;
    }

    private function redactPayload(array $payload): array
    {
        $redacted = $payload;

        if (array_key_exists('token', $redacted)) {
            $redacted['token'] = '***redacted***';
        }

        if (isset($redacted['payer']) && is_array($redacted['payer'])) {
            $payer = $redacted['payer'];

            if (isset($payer['identification']) && is_array($payer['identification'])) {
                $identification = $payer['identification'];
                if (array_key_exists('number', $identification)) {
                    $identification['number'] = '***redacted***';
                }
                $payer['identification'] = $identification;
            }

            $redacted['payer'] = $payer;
        }

        return $redacted;
    }
}
