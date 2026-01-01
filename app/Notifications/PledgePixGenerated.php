<?php

namespace App\Notifications;

use App\Domain\Pledge\Pledge;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PledgePixGenerated extends Notification
{
    use Queueable;

    /**
     * @param array{copy_paste?:string,expires_at?:string|null,qr_code_base64?:string|null} $pix
     */
    public function __construct(
        private readonly Pledge $pledge,
        private readonly array $pix,
    ) {
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
            ->subject('Seu Pix para apoiar uma campanha')
            ->markdown('emails.pledges.pix-generated', [
                'recipientName' => $recipientName,
                'campaignTitle' => $this->pledge->campaign->title,
                'campaignUrl' => $campaignUrl,
                'amount' => $this->pledge->formatted_amount,
                'copyPaste' => (string) ($this->pix['copy_paste'] ?? ''),
                'expiresAt' => $this->pix['expires_at'] ?? null,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->pledge->loadMissing(['campaign']);

        return [
            'type' => 'pledge_pix_generated',
            'pledge_id' => $this->pledge->id,
            'campaign_id' => $this->pledge->campaign_id,
            'campaign_slug' => $this->pledge->campaign->slug,
        ];
    }
}
