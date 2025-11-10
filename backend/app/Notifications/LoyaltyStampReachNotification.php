<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LoyaltyStampReachNotification extends Notification
{
    use Queueable;

    protected $loyaltyCard;
    protected $user;

    public function __construct($loyaltyCard, $user)
    {
        $this->loyaltyCard = $loyaltyCard;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'loyalty_stamp_reach',
            'title' => 'Loyalty Card Stamp Reach',
            'message' => "Customer {$this->user->name} has reached {$this->loyaltyCard->stamps_count}/5 stamps on their loyalty card.",
            'loyalty_card_id' => $this->loyaltyCard->id,
            'user_id' => $this->user->id,
            'stamps_count' => $this->loyaltyCard->stamps_count,
            'icon' => 'fas fa-star',
            'color' => 'warning',
            'action_url' => route('admin.loyalty.index')
        ];
    }
}
