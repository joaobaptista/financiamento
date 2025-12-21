<?php

namespace App\Services\Money;

final class Money
{
    /** @throws \InvalidArgumentException */
    public static function toCents(int|float|string $amount): int
    {
        if (is_string($amount)) {
            $amount = str_replace(' ', '', $amount);

            $hasComma = str_contains($amount, ',');
            $hasDot = str_contains($amount, '.');

            // pt-BR style: 1.234,56
            if ($hasComma && $hasDot) {
                $amount = str_replace('.', '', $amount);
                $amount = str_replace(',', '.', $amount);
            } elseif ($hasComma) {
                // 1234,56
                $amount = str_replace(',', '.', $amount);
            }
            // If it's only dot, assume dot is decimal separator (e.g. 10.50)
        }

        $float = (float) $amount;

        if ($float < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }

        return (int) round($float * 100);
    }
}
