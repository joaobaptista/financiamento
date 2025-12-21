<?php

namespace App\Data\Payments;

final readonly class PaymentResultData
{
    public function __construct(
        public bool $success,
        public ?string $paymentId,
        public int $amount,
        public string $status,
        public ?string $message = null,
        /** @var array<string, mixed> */
        public array $raw = [],
    ) {
    }
}
