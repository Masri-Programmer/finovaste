<?php

namespace App\Observers;

use App\Models\ListingFaq;
use App\Mail\ListingUpdated;
use Illuminate\Support\Facades\Mail;

class ListingFaqObserver
{
    public function created(ListingFaq $faq): void
    {
        $listing = $faq->listing;
        
        foreach ($listing->subscriptions as $subscriber) {
             if ($subscriber->email === $faq->user->email) {
                continue;
            }

            $user = \App\Models\User::where('email', $subscriber->email)->first();
            $locale = $user ? $user->locale : config('app.locale');

            Mail::to($subscriber->email)->queue(new ListingUpdated($listing, [
                'type' => 'faq',
                'key' => 'updates.new_question_body', // "A new question has been asked: :question"
                'params' => ['question' => Str::limit($faq->question, 50)],
                'subject_key' => 'updates.new_question_subject',
                'url' => route('listings.show', $listing) . '#faqs'
            ], $locale));
        }
    }

    public function updated(ListingFaq $faq): void
    {
        // Check if answer was added
        if ($faq->wasChanged('answer') && !empty($faq->answer)) {
            $listing = $faq->listing;

            foreach ($listing->subscriptions as $subscriber) {
                $user = \App\Models\User::where('email', $subscriber->email)->first();
                $locale = $user ? $user->locale : config('app.locale');

                Mail::to($subscriber->email)->queue(new ListingUpdated($listing, [
                    'type' => 'faq_answer',
                    'key' => 'updates.question_answered_body', // "A question has been answered: :answer"
                    'params' => ['answer' => Str::limit($faq->answer, 50)],
                    'subject_key' => 'updates.question_answered_subject',
                    'url' => route('listings.show', $listing) . '#faqs'
                ], $locale));
            }
        }
    }
}
