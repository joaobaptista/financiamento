<?php

namespace App\Http\Controllers;

use App\Actions\Pledge\ConfirmPayment;
use App\Actions\Pledge\CreatePledge;
use App\Contracts\Payments\PaymentService;
use App\Data\Pledge\CreatePledgeData;
use App\Enums\PledgeStatus;
use App\Http\Requests\StorePledgeRequest;
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

            // Processar pagamento (mock)
            $paymentResult = $paymentService->processPayment($amount, $paymentMethod, [
                'campaign_id' => (int) $validated['campaign_id'],
                'user_id' => auth()->id(),
                'pledge_id' => $pledge->id,
            ]);

            if ($paymentResult->success) {
                $pledge->provider_payment_id = $paymentResult->paymentId;
                if (is_array($paymentResult->nextAction)) {
                    $pledge->provider_payload = $paymentResult->nextAction;
                }
                $pledge->save();

                if ($paymentResult->status === PledgeStatus::Paid->value) {
                    $confirmPayment->execute($pledge, $paymentResult->paymentId);
                    return response()->json(["message" => "Pagamento confirmado!"], 201);
                }

                return response()->json([
                    'message' => 'Pix gerado. Pague e aguarde a confirmação.',
                    'pledge_id' => $pledge->id,
                ], 201);
            } else {
                return redirect()->back()
                    ->with('error', 'Erro ao processar pagamento. Tente novamente.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
