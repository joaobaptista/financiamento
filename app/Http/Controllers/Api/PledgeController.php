<?php

namespace App\Http\Controllers\Api;

use App\Actions\Pledge\ConfirmPayment;
use App\Actions\Pledge\CreatePledge;
use App\Contracts\Payments\PaymentService;
use App\Data\Pledge\CreatePledgeData;
use App\Domain\Pledge\Pledge;
use App\Enums\PledgeStatus;
use App\Http\Requests\StorePledgeRequest;
use App\Notifications\PledgePaymentConfirmed;
use App\Notifications\PledgePixGenerated;
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
        $paymentMethod = (string) $validated['payment_method'];

        try {
            $data = new CreatePledgeData(
                campaignId: (int) $validated['campaign_id'],
                userId: auth()->id(),
                amount: $amount,
                rewardId: $validated['reward_id'] ?? null,
                paymentMethod: $paymentMethod,
            );

            $pledge = $createPledge->execute($data);

            $paymentResult = $paymentService->processPayment($amount, $paymentMethod, [
                'campaign_id' => (int) $validated['campaign_id'],
                'user_id' => auth()->id(),
                'pledge_id' => $pledge->id,
                'payer_email' => (string) (auth()->user()?->email ?? ''),
                'description' => 'Apoio campanha #' . (int) $validated['campaign_id'],
                'idempotency_key' => 'pledge_' . $pledge->id,
                'card_token' => $validated['card_token'] ?? null,
                'installments' => $validated['installments'] ?? null,
            ]);

            if (! $paymentResult->success) {
                $pledge->markAsCanceled();
                return response()->json([
                    'message' => 'Erro ao processar pagamento. Tente novamente.',
                ], 422);
            }

            // Persist provider payment id / payload for tracking
            $pledge->provider_payment_id = $paymentResult->paymentId;
            if (is_array($paymentResult->nextAction)) {
                $pledge->provider_payload = $paymentResult->nextAction;
            }
            $pledge->save();

            // Card (mock) confirms immediately; Pix stays pending until confirmation.
            if ($paymentResult->status === PledgeStatus::Paid->value) {
                $wasPaid = $pledge->status === PledgeStatus::Paid;
                $confirmPayment->execute($pledge, $paymentResult->paymentId);
                $pledge->refresh();

                if (!$wasPaid && $pledge->status === PledgeStatus::Paid && auth()->user()) {
                    auth()->user()->notify(new PledgePaymentConfirmed($pledge));
                }
            } elseif ($paymentResult->paymentMethod === 'pix' && is_array($paymentResult->nextAction) && auth()->user()) {
                auth()->user()->notify(new PledgePixGenerated($pledge, $paymentResult->nextAction));
            }

            return response()->json([
                'ok' => true,
                'pledge_id' => $pledge->id,
                'status' => $pledge->status->value,
                'payment' => [
                    'method' => $paymentResult->paymentMethod,
                    'status' => $paymentResult->status,
                    'payment_id' => $paymentResult->paymentId,
                    'next_action' => $paymentResult->nextAction,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function confirm(int $id, ConfirmPayment $confirmPayment)
    {
        $userId = (int) auth()->id();

        /** @var Pledge $pledge */
        $pledge = Pledge::query()
            ->whereKey($id)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Only pending pledges can be confirmed
        if ($pledge->status === PledgeStatus::Canceled || $pledge->status === PledgeStatus::Refunded) {
            return response()->json([
                'message' => 'Este apoio não pode ser confirmado.',
            ], 422);
        }

        $confirmPayment->execute($pledge, $pledge->provider_payment_id);

        return response()->json([
            'ok' => true,
            'status' => PledgeStatus::Paid->value,
        ]);
    }
}
