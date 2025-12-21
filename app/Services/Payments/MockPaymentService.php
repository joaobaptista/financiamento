<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentService;
use App\Data\Payments\PaymentResultData;

class MockPaymentService implements PaymentService
{
    public function processPayment(int $amount, array $metadata = []): PaymentResultData
    {
        // Simula processamento de pagamento
        // No MVP, sempre retorna sucesso

        return new PaymentResultData(
            success: true,
            paymentId: 'mock_' . uniqid(),
            amount: $amount,
            status: 'paid',
            message: 'Pagamento simulado processado com sucesso',
            raw: [
                'metadata' => $metadata,
            ],
        );
    }

    public function refundPayment(string $paymentId): PaymentResultData
    {
        // Simula estorno
        return new PaymentResultData(
            success: true,
            paymentId: $paymentId,
            amount: 0,
            status: 'refunded',
            message: 'Estorno simulado processado com sucesso',
        );
    }
}
