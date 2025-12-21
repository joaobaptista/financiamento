<?php

namespace App\Http\Controllers;

use App\Domain\Campaign\Campaign;
use App\Actions\CreatePledge;
use App\Actions\ConfirmPayment;
use App\Services\Payments\MockPaymentService;
use App\Services\Money\Money;
use Illuminate\Http\Request;

class PledgeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'amount' => 'required|numeric|min:1',
            'reward_id' => 'nullable|exists:rewards,id',
        ]);

        $campaign = Campaign::findOrFail($validated['campaign_id']);
        $amount = Money::toCents($validated['amount']);

        try {
            // Criar pledge
            $createPledgeAction = new CreatePledge();
            $pledge = $createPledgeAction->execute(
                $campaign,
                auth()->user(),
                $amount,
                $validated['reward_id'] ?? null
            );

            // Processar pagamento (mock)
            $paymentService = new MockPaymentService();
            $paymentResult = $paymentService->processPayment($amount, [
                'campaign_id' => $campaign->id,
                'user_id' => auth()->id(),
            ]);

            if ($paymentResult->success) {
                // Confirmar pagamento
                $confirmPaymentAction = new ConfirmPayment();
                $confirmPaymentAction->execute($pledge, $paymentResult->paymentId);

                return redirect()->route('campaigns.show', $campaign->slug)
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
