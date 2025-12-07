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
    public $updateData;

    /**
     * Create a new message instance.
     *
     * @param Listing $listing
     * @param array $updateData ['key' => string, 'params' => array, 'url' => ?string, 'subject_key' => ?string]
     * @param string|null $locale
     */
    public function __construct(Listing $listing, array $updateData, ?string $locale = null)
    {
        $this->listing = $listing;
        $this->updateData = $updateData;

        if ($locale) {
            $this->locale($locale);
        }
    }

    public function build()
    {
        $subjectKey = $this->updateData['subject_key'] ?? 'updates.email_subject_general';

        return $this->subject(__($subjectKey, ['title' => $this->listing->title]))
                    ->markdown('emails.listings.updated');
    }
}