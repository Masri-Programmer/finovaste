<?php

namespace App\Mail;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ListingUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $listing;

    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    public function build()
{
    $subjectKey = $this->update->type === 'manual' 
        ? 'updates.email_subject_manual' 
        : 'updates.email_subject_system';

    return $this->subject(__($subjectKey, ['title' => $this->listing->title]))
                ->markdown('emails.listings.updated');
}
}