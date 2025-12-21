<?php

namespace App\Http\Controllers;

use App\Actions\Pledge\ConfirmPayment;
use App\Actions\Pledge\CreatePledge;
use App\Contracts\Payments\PaymentService;
use App\Data\Pledge\CreatePledgeData;
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

        try {
            // Criar pledge
            $data = new CreatePledgeData(
                campaignId: (int) $validated['campaign_id'],
                userId: auth()->id(),
                amount: $amount,
                rewardId: $validated['reward_id'] ?? null,
            );

            $pledge = $createPledge->execute($data);

            // Processar pagamento (mock)
            $paymentResult = $paymentService->processPayment($amount, [
                'campaign_id' => (int) $validated['campaign_id'],
                'user_id' => auth()->id(),
            ]);

            if ($paymentResult->success) {
                // Confirmar pagamento
                $confirmPayment->execute($pledge, $paymentResult->paymentId);

                return redirect()->route('campaigns.show', $pledge->campaign->slug)
                    ->with('success', 'Apoio realizado com sucesso! Obrigado por apoiar este projeto.');
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
