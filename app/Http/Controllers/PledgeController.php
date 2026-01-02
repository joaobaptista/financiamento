<?php

namespace App\Http\Controllers;

use App\Actions\Pledge\ConfirmPayment;
use App\Actions\Pledge\CreatePledge;
use App\Contracts\Payments\PaymentService;
use App\Data\Pledge\CreatePledgeData;
use App\Enums\PledgeStatus;
use App\Http\Requests\StorePledgeRequest;
use App\Notifications\PledgePaymentConfirmed;
use App\Notifications\PledgePixGenerated;
use App\Services\Money\Money;

class PledgeController extends Controller
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
        $paymentMethod = (string) ($validated['payment_method'] ?? 'card');

        try {
            // Criar pledge
            $data = new CreatePledgeData(
                campaignId: (int) $validated['campaign_id'],
                userId: auth()->id(),
                amount: $amount,
                rewardId: $validated['reward_id'] ?? null,
                paymentMethod: $paymentMethod,
            );

            $pledge = $createPledge->execute($data);

            // Processar pagamento
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
                'issuer_id' => $validated['issuer_id'] ?? null,
                'payer_identification_type' => $validated['payer_identification_type'] ?? null,
                'payer_identification_number' => $validated['payer_identification_number'] ?? null,
            ]);

            if ($paymentResult->success) {
                $pledge->provider_payment_id = $paymentResult->paymentId;
                if (is_array($paymentResult->nextAction)) {
                    $pledge->provider_payload = $paymentResult->nextAction;
                }
                $pledge->save();

                if ($paymentResult->status === PledgeStatus::Paid->value) {
                    $wasPaid = $pledge->status === PledgeStatus::Paid;
                    $confirmPayment->execute($pledge, $paymentResult->paymentId);

                    if (!$wasPaid && $pledge->fresh()->status === PledgeStatus::Paid && auth()->user()) {
                        auth()->user()->notify(new PledgePaymentConfirmed($pledge));
                    }
                    return response()->json(["message" => "Pagamento confirmado!"], 201);
                }

                if ($paymentResult->paymentMethod === 'pix' && is_array($paymentResult->nextAction) && auth()->user()) {
                    auth()->user()->notify(new PledgePixGenerated($pledge, $paymentResult->nextAction));
                }

                return response()->json([
                    'message' => 'Pix gerado. Pague e aguarde a confirmação.',
                    'pledge_id' => $pledge->id,
                ], 201);
            } else {
                $pledge->markAsCanceled();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Erro ao processar pagamento. Tente novamente.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
