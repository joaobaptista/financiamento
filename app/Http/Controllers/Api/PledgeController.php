<?php

namespace App\Http\Controllers\Api;

use App\Actions\Pledge\ConfirmPayment;
use App\Actions\Pledge\CreatePledge;
use App\Contracts\Payments\PaymentService;
use App\Data\Pledge\CreatePledgeData;
use App\Http\Requests\StorePledgeRequest;
use App\Services\Money\Money;

class PledgeController
{
    public function store(
        StorePledgeRequest $request,
        PaymentService $paymentService,
        CreatePledge $createPledge,
        ConfirmPayment $confirmPayment,
    )
    {
        $validated = $request->validated();

        $amount = Money::toCents($validated['amount']);

        try {
            $data = new CreatePledgeData(
                campaignId: (int) $validated['campaign_id'],
                userId: auth()->id(),
                amount: $amount,
                rewardId: $validated['reward_id'] ?? null,
            );

            $pledge = $createPledge->execute($data);

            $paymentResult = $paymentService->processPayment($amount, [
                'campaign_id' => (int) $validated['campaign_id'],
                'user_id' => auth()->id(),
            ]);

            if (! $paymentResult->success) {
                $pledge->markAsCanceled();
                return response()->json([
                    'message' => 'Erro ao processar pagamento. Tente novamente.',
                ], 422);
            }

            $confirmPayment->execute($pledge, $paymentResult->paymentId);

            return response()->json([
                'ok' => true,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
