<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePledgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $isMercadoPago = (string) config('payments.driver', 'mock') === 'mercadopago';

        $cardTokenRules = ['nullable', 'string'];
        if ($isMercadoPago) {
            $cardTokenRules[] = 'required_if:payment_method,card';
        }

        return [
            'campaign_id' => 'required|exists:campaigns,id',
            'amount' => 'required|numeric|min:1',
            'reward_id' => 'nullable|exists:rewards,id',
            'payment_method' => 'required|in:pix,card',

            // Card (tokenized). In mock mode we keep it optional.
            'card_token' => $cardTokenRules,
            'installments' => 'nullable|integer|min:1|max:12',
        ];
    }
}
