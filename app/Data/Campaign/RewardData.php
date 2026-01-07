<?php

namespace App\Data\Campaign;

final readonly class RewardData
{
    /**
     * @param array<string, int>|null $fretes Array com regiao => valor em centavos
     */
    public function __construct(
        public string $title,
        public string $description,
        public int $minAmount,
        public ?int $quantity,
        public ?array $fretes = null, // ['norte' => 1500, 'nordeste' => 1200, ...]
    ) {
    }
}
