<?php

namespace App\Actions;

use App\Domain\Pledge\Pledge;
use App\Domain\Campaign\Campaign;

class ConfirmPayment
{
    public function execute(Pledge $pledge, ?string $paymentId = null): bool
    {
        if ($pledge->status === 'paid') {
            return true; // Já está pago
        }

        // Marcar pledge como pago
        $pledge->markAsPaid($paymentId);

        // Atualizar pledged_amount da campanha
        $campaign = $pledge->campaign;
        $campaign->pledged_amount += $pledge->amount;
        $campaign->save();

        return true;
    }
}
