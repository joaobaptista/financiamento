<?php

namespace App\Http\Controllers;

use App\Actions\Pledge\ConfirmPayment;
use App\Actions\Pledge\CreatePledge;
use App\Contracts\Payments\PaymentService;
use App\Data\Pledge\CreatePledgeData;
use App\Services\Money\Money;
use Illuminate\Http\Request;

class PledgeController extends Controller
{
    public function store(Request $request, PaymentService $paymentService)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'amount' => 'required|numeric|min:1',
            'reward_id' => 'nullable|exists:rewards,id',
        ]);

        $amount = Money::toCents($validated['amount']);

        try {
            // Criar pledge
            $data = new CreatePledgeData(
                campaignId: (int) $validated['campaign_id'],
                userId: auth()->id(),
                amount: $amount,
                rewardId: $validated['reward_id'] ?? null,
            );

            $createPledgeAction = new CreatePledge();
            $pledge = $createPledgeAction->execute($data);

            // Processar pagamento (mock)
            $paymentResult = $paymentService->processPayment($amount, [
                'campaign_id' => (int) $validated['campaign_id'],
                'user_id' => auth()->id(),
            ]);

            if ($paymentResult->success) {
                // Confirmar pagamento
                $confirmPaymentAction = new ConfirmPayment();
                $confirmPaymentAction->execute($pledge, $paymentResult->paymentId);

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
