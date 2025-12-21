<?php

namespace App\Actions\Pledge;

use App\Domain\Pledge\Pledge;
use App\Enums\PledgeStatus;

class ConfirmPayment
{
    public function execute(Pledge $pledge, ?string $paymentId = null): bool
    {
        if ($pledge->status === PledgeStatus::Paid) {
            return true;
        }

        $pledge->markAsPaid($paymentId);

        $campaign = $pledge->campaign;
        $campaign->pledged_amount += $pledge->amount;
        $campaign->save();

        return true;
    }
}
