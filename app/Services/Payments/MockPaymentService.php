<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentService;
use App\Data\Payments\PaymentResultData;
use App\Enums\PledgeStatus;

class MockPaymentService implements PaymentService
{
    public function processPayment(int $amount, string $paymentMethod, array $metadata = []): PaymentResultData
    {
        $paymentId = 'mock_' . uniqid();

        // Pix: retorna pagamento pendente + instruções (copia e cola) para o usuário.
        if ($paymentMethod === 'pix') {
            $copyPaste = '00020126580014BR.GOV.BCB.PIX0136' . strtoupper(bin2hex(random_bytes(12))) . '5204000053039865405' . str_pad((string) round($amount / 100, 2), 5, '0', STR_PAD_LEFT) . '5802BR5920ORIGO MOCK PIX6009SAO PAULO62290525' . strtoupper(bin2hex(random_bytes(8))) . '6304' . strtoupper(bin2hex(random_bytes(2)));

            return new PaymentResultData(
                success: true,
                paymentId: $paymentId,
                amount: $amount,
                status: PledgeStatus::Pending->value,
                paymentMethod: 'pix',
                nextAction: [
                    'type' => 'pix',
                    'copy_paste' => $copyPaste,
                    'expires_at' => now()->addMinutes(30)->toIso8601String(),
                ],
                message: 'Pix gerado. Pague e aguarde a confirmação.',
                raw: [
                    'metadata' => $metadata,
                ],
            );
        }

        // Cartão: no mock, confirma imediatamente.
        return new PaymentResultData(
            success: true,
            paymentId: $paymentId,
            amount: $amount,
            status: PledgeStatus::Paid->value,
            paymentMethod: 'card',
            nextAction: null,
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
            status: PledgeStatus::Refunded->value,
            message: 'Estorno simulado processado com sucesso',
        );
    }
}
