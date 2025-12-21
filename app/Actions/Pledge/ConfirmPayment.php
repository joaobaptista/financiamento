<?php

namespace App\Actions\Pledge;

use App\Domain\Pledge\Pledge;

class ConfirmPayment
{
    public function execute(Pledge $pledge, ?string $paymentId = null): bool
    {
        if ($pledge->status === 'paid') {
            return true;
        }

        $pledge->markAsPaid($paymentId);

        $campaign = $pledge->campaign;
        $campaign->pledged_amount += $pledge->amount;
        $campaign->save();

        return true;
    }
}
