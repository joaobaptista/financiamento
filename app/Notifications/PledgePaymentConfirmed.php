<?php

namespace App\Notifications;

use App\Domain\Pledge\Pledge;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PledgePaymentConfirmed extends Notification
{
    use Queueable;

    public function __construct(private readonly Pledge $pledge)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->pledge->loadMissing(['campaign']);

        $baseUrl = rtrim((string) config('app.url'), '/');
            $recipientName = (string) ($notifiable->name ?? '');
        $campaignUrl = $baseUrl . '/campaigns/' . $this->pledge->campaign->slug;

        return (new MailMessage)
            ->subject('Apoio confirmado')
            ->markdown('emails.pledges.payment-confirmed', [
                'recipientName' => $recipientName,
                'campaignTitle' => $this->pledge->campaign->title,
                'campaignUrl' => $campaignUrl,
                'amount' => $this->pledge->formatted_amount,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->pledge->loadMissing(['campaign']);

        return [
            'type' => 'pledge_payment_confirmed',
            'pledge_id' => $this->pledge->id,
            'campaign_id' => $this->pledge->campaign_id,
            'campaign_slug' => $this->pledge->campaign->slug,
        ];
    }
}
