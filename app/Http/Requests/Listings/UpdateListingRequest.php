<?php

namespace App\Http\Requests\Listings;

use App\Models\Listing;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\AuctionListing;
use App\Models\DonationListing;
use Illuminate\Validation\Rule;


class UpdateListingRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        // Convert date format from frontend (similar to StoreListingRequest)
        $parseDate = function ($dateInput) {
            if (is_array($dateInput) && !empty($dateInput)) {
                if (isset($dateInput['year'], $dateInput['month'], $dateInput['day'])) {
                    return "{$dateInput['year']}-{$dateInput['month']}-{$dateInput['day']}";
                }
                if (isset($dateInput[0]) && is_string($dateInput[0])) {
                    return $dateInput[0];
                }
            }
            return is_string($dateInput) ? $dateInput : null;
        };

        $expires = $parseDate($this->expires_at);
        $ends    = $parseDate($this->ends_at);

        // For auctions, ensure expires_at matches ends_at if provided
        if ($ends && !$expires) {
            $expires = $ends;
        }

        if ($expires) {
            $this->merge([
                'expires_at' => \Carbon\Carbon::parse($expires)->toDateTimeString(),
            ]);
        }

        if ($ends) {
            $this->merge([
                'ends_at' => \Carbon\Carbon::parse($ends)->toDateTimeString(),
            ]);
        }

    }

    public function authorize(): bool

    {
        $listing = $this->route('listing');
        return $listing && $this->user()->id === $listing->user_id;
    }

    public function rules(): array
    {
        $userLocale = app()->getLocale();
        $listing = $this->route('listing');
        
        if (!$listing->relationLoaded('listable')) {
            $listing->load('listable');
        }

        $rules = [
            'title' => 'required|array',
            'title.' . $userLocale => 'required|string|max:255',
            'title.*' => 'nullable|string|max:255',
            'description' => 'required|array',
            'description.' . $userLocale => 'required|string',
            'description.*' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'address_id' => 'nullable|exists:addresses,id',
            'expires_at' => 'nullable|date|after:now',
        ];

        $specificRules = [];

        if ($listing->listable instanceof AuctionListing) {
            $specificRules = [
                'start_price'    => 'required|numeric|min:0',
                'ends_at'        => 'required|date',
                'reserve_price'  => 'nullable|numeric|gte:start_price',
                'purchase_price' => 'nullable|numeric|gte:start_price',
            ];
        } elseif ($listing->listable instanceof DonationListing) {
            $specificRules = [
                'target'    => 'required|numeric|min:0', // Critical: Added this
                'is_capped' => 'boolean',
            ];
        }

        $mediaRules = [
            'media_to_delete'   => 'nullable|array',
            'media_to_delete.*' => [
                'required',
                'integer',
                Rule::exists('media', 'id')->where(function ($query) use ($listing) {
                    $query->where('model_type', Listing::class)
                          ->where('model_id', $listing->id);
                }),
            ],
            'images'    => 'nullable|array',
            'images.*'  => 'file|mimetypes:image/jpeg,image/png,image/webp|max:10240',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:5120',
            'videos'    => 'nullable|array',
            'videos.*'  => 'file|mimetypes:video/mp4,video/quicktime|max:51200',
        ];

        return array_merge($rules, $specificRules, $mediaRules);
    }

    public function getCommonData(): array
    {
        return $this->safe()->only([
            'title',
            'description',
            'category_id',
            'address_id',
            'expires_at',
        ]);
    }


    public function getSpecificData(): array
    {
        $listing = $this->route('listing');

        if ($listing->listable instanceof AuctionListing) {
            return $this->safe()->only([
                'start_price',
                'ends_at',
                'reserve_price',
                'purchase_price'
            ]);
        }
        
        if ($listing->listable instanceof DonationListing) {
            return $this->safe()->only([
                'target',
                'is_capped'
            ]);
        }

        return [];
    }
}