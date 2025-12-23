<?php

namespace App\Data\Pledge;

final readonly class CreatePledgeData
{
    public function __construct(
        public int $campaignId,
        public int $userId,
        public int $amount,
        public ?int $rewardId,
        public string $paymentMethod,
    ) {
    }
}
