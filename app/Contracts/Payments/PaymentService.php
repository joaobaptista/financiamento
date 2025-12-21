<?php

namespace App\Contracts\Payments;

use App\Data\Payments\PaymentResultData;

interface PaymentService
{
    /** @param array<string, mixed> $metadata */
    public function processPayment(int $amount, array $metadata = []): PaymentResultData;

    public function refundPayment(string $paymentId): PaymentResultData;
}
