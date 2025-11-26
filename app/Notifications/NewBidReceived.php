<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Listing;
use App\Models\AuctionListing;

class NewBidReceived extends Notification
{
    use Queueable;

    public $listing;
    public $bidAmount;

    public function __construct(Listing $listing, $bidAmount)
    {
        $this->listing = $listing;
        $this->bidAmount = $bidAmount;
    }

    public function via($notifiable)
    {
        if (! $notifiable->wantsNotification('new_bid')) {
            return [];
        }

        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_bid',
            'listing_id' => $this->listing->id,
            'listing_title' => $this->listing->title,
            'amount' => $this->bidAmount,
            'message' => "New bid of {$this->bidAmount} on {$this->listing->title}",
            'url' => route('listings.show', $this->listing->id),
        ];
    }
}