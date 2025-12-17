<?php

namespace App\Services\Payments;

class MockPaymentService
{
    public function processPayment(int $amount, array $metadata = []): array
    {
        // Simula processamento de pagamento
        // No MVP, sempre retorna sucesso

        return [
            'success' => true,
            'payment_id' => 'mock_' . uniqid(),
            'amount' => $amount,
            'status' => 'paid',
            'message' => 'Pagamento simulado processado com sucesso',
        ];
    }

    public function refundPayment(string $paymentId): array
    {
        // Simula estorno
        return [
            'success' => true,
            'payment_id' => $paymentId,
            'status' => 'refunded',
            'message' => 'Estorno simulado processado com sucesso',
        ];
    }
}
