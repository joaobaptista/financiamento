<?php

namespace App\Data\Campaign;

final readonly class RewardData
{
    public function __construct(
        public string $title,
        public string $description,
        public int $minAmount,
        public ?int $quantity,
    ) {
    }
}
