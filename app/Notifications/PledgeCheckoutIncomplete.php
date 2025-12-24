<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PledgeCheckoutIncomplete extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $projectTitle,
        private readonly string $projectUrl,
        private readonly ?string $supportCenterUrl = null,
        private readonly ?string $supportUrl = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $logoUrl = $baseUrl . '/img/logo.svg';

        return (new MailMessage)
            ->subject('Finalize seu apoio')
            ->markdown('emails.pledges.checkout-incomplete', [
                'logoUrl' => $logoUrl,
                'projectTitle' => $this->projectTitle,
                'projectUrl' => $this->projectUrl,
                'supportCenterUrl' => $this->supportCenterUrl,
                'supportUrl' => $this->supportUrl,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'pledge_checkout_incomplete',
            'project_title' => $this->projectTitle,
            'project_url' => $this->projectUrl,
        ];
    }
}
