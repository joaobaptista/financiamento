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

    /**
     * Lista todos os apoios (pledges) do usuário autenticado, incluindo dados da campanha.
     */
    public function myPledges()
    {
        $userId = (int) auth()->id();
        $pledges = Pledge::with(['campaign'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($pledge) {
                return [
                    'id' => $pledge->id,
                    'status' => $pledge->status->value,
                    'amount' => $pledge->amount,
                    'paid_at' => $pledge->paid_at,
                    'campaign' => [
                        'id' => $pledge->campaign->id ?? null,
                        'title' => $pledge->campaign->title ?? null,
                        'cover_image_path' => $pledge->campaign->cover_image_path ?? null,
                    ],
                ];
            });
        return response()->json(['ok' => true, 'pledges' => $pledges]);
    }
    public function show(int $id)
    {
        $userId = (int) auth()->id();

        /** @var Pledge $pledge */
        $pledge = Pledge::query()
            ->whereKey($id)
            ->where('user_id', $userId)
            ->firstOrFail();

        return response()->json([
            'ok' => true,
            'pledge_id' => $pledge->id,
            'status' => $pledge->status->value,
            'payment' => [
                'method' => $pledge->payment_method,
                'provider_payment_id' => $pledge->provider_payment_id,
            ],
        ]);
    }

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
                'payment_method_id' => $validated['payment_method_id'] ?? null,
                'payer_identification_type' => $validated['payer_identification_type'] ?? null,
                'payer_identification_number' => $validated['payer_identification_number'] ?? null,
            ]);

            if (! $paymentResult->success) {
                $pledge->markAsCanceled();

                $response = [
                    'message' => 'Erro ao processar pagamento. Tente novamente.',
                ];

                if ((bool) config('app.debug') && is_array($paymentResult->raw)) {
                    $response['provider'] = $paymentResult->raw;
                }

                return response()->json($response, 422);
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
        if ((string) config('payments.driver', 'mock') === 'mercadopago') {
            return response()->json([
                'message' => 'No Mercado Pago, a confirmação é automática. Aguarde ou atualize a página.',
            ], 422);
        }

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
