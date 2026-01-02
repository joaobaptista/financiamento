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
    private string $baseUrl;
    private ?string $accessToken;

    public function __construct()
    {
        $this->baseUrl = (string) config('mercadopago.base_url', 'https://api.mercadopago.com' );
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
                message: 'Mercado Pago não configurado.',
            );
        }

        $transactionAmount = round($amount / 100, 2);
        $payerEmail = $this->resolvePayerEmail($metadata);
        $currencyId = (string) config('mercadopago.currency', 'BRL');
        $externalReference = isset($metadata['pledge_id']) ? 'pledge_' . $metadata['pledge_id'] : null;
        $idempotencyKey = (string) ($metadata['idempotency_key'] ?? Str::uuid()->toString());

        $payload = match ($paymentMethod) {
            'pix' => $this->buildPixPayload($transactionAmount, (string)($metadata['description'] ?? 'Apoio'), $payerEmail, $externalReference, $currencyId, $metadata),
            'card' => $this->buildCardPayload($transactionAmount, (string)($metadata['description'] ?? 'Apoio'), $payerEmail, $externalReference, $metadata, $currencyId),
            default => null,
        };

        if (empty($payload)) {
            return new PaymentResultData(success: false, paymentId: null, amount: $amount, status: PledgeStatus::Canceled->value, paymentMethod: $paymentMethod, message: 'Dados inválidos.');
        }

        try {
            $response = Http::baseUrl($this->baseUrl)
                ->withToken($this->accessToken)
                ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
                ->post('/v1/payments', $payload);

            if (!$response->successful()) {
                return new PaymentResultData(success: false, paymentId: null, amount: $amount, status: PledgeStatus::Canceled->value, paymentMethod: $paymentMethod, raw: $response->json());
            }

            $body = $response->json();
            $status = $this->mapMercadoPagoStatusToPledgeStatus($body['status'] ?? null);

            $nextAction = null;
            if ($paymentMethod === 'pix') {
                $transactionData = $body['point_of_interaction']['transaction_data'] ?? null;
                if ($transactionData) {
                    $nextAction = [
                        'type' => 'pix',
                        'copy_paste' => $transactionData['qr_code'],
                        'qr_code_base64' => $transactionData['qr_code_base64'] ?? null,
                        'expires_at' => $body['date_of_expiration'] ?? null,
                    ];
                }
            }

            return new PaymentResultData(
                success: true,
                paymentId: (string)$body['id'],
                amount: $amount,
                status: $status,
                paymentMethod: $paymentMethod,
                nextAction: $nextAction,
                raw: $body
            );
        } catch (\Throwable $e) {
            return new PaymentResultData(success: false, paymentId: null, amount: $amount, status: PledgeStatus::Canceled->value, paymentMethod: $paymentMethod, message: $e->getMessage());
        }
    }

    private function buildPixPayload($transactionAmount, $description, $payerEmail, $externalReference, $currencyId, $metadata): array
    {
        $payload = [
            'transaction_amount' => $transactionAmount,
            'currency_id' => $currencyId,
            'description' => $description,
            'payment_method_id' => 'pix',
            'payer' => ['email' => $payerEmail],
        ];

        if (!empty($metadata['payer_identification_type']) && !empty($metadata['payer_identification_number'])) {
            $payload['payer']['identification'] = [
                'type' => $metadata['payer_identification_type'],
                'number' => $metadata['payer_identification_number'],
            ];
        }

        if ($externalReference) $payload['external_reference'] = $externalReference;

        return $payload;
    }

    private function buildCardPayload($transactionAmount, $description, $payerEmail, $externalReference, $metadata, $currencyId): array
    {
        $token = (string)($metadata['card_token'] ?? '');
        if ($token === '') return [];

        $payload = [
            'transaction_amount' => $transactionAmount,
            'currency_id' => $currencyId,
            'description' => $description,
            'token' => $token,
            'installments' => max(1, min(12, (int)($metadata['installments'] ?? 1))),
            'payer' => ['email' => $payerEmail],
        ];

        if (!empty($metadata['payer_identification_type']) && !empty($metadata['payer_identification_number'])) {
            $payload['payer']['identification'] = [
                'type' => $metadata['payer_identification_type'],
                'number' => $metadata['payer_identification_number'],
            ];
        }

        if ($externalReference) $payload['external_reference'] = $externalReference;
        if (!empty($metadata['payment_method_id'])) $payload['payment_method_id'] = $metadata['payment_method_id'];

        return $payload;
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

    private function resolvePayerEmail(array $metadata): string
    {
        $email = trim((string)($metadata['payer_email'] ?? ''));
        if ($email === '') $email = 'payer@example.com';
        
        $accessToken = (string)$this->accessToken;
        if (Str::startsWith($accessToken, ['TEST-', 'TEST_', 'TEST']) && !Str::endsWith($email, '@testuser.com')) {
            $email = config('mercadopago.test_payer_email', 'test_user_123@testuser.com');
        }
        return $email;
    }
}
