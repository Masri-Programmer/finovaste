<?php

namespace App\Http\Requests\Listings;

use App\Models\Listing;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\AuctionListing;
use App\Models\DonationListing;
use Illuminate\Validation\Rule;

class UpdateListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $listing = $this->route('listing');

        return $listing && $this->user()->id === $listing->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // 1. --- Common Rules ---
        $rules = [
            'title' => 'required|array',
            'title.*' => 'nullable|string|max:255',
            'description' => 'required|array',
            'description.*' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ];

        // 2. --- Get the Listing model from the route ---
        // We load 'listable' to check its instance type
        $listing = $this->route('listing')->loadMissing('listable');

        // 3. --- Type-Specific Rules ---
        $specificRules = [];

        $mediaRules = [
            'media_to_delete'   => 'nullable|array',
            'media_to_delete.*' => [
                'required',
                'integer',
                // This magic rule checks two things:
                // 1. The ID exists in the 'media' table.
                // 2. It belongs to the *exact listing* we are editing.
                Rule::exists('media', 'id')->where(function ($query) {
                    $query->where('model_type', Listing::class)
                        ->where('model_id', $this->route('listing')->id);
                }),
            ],
        ];
    
        if ($listing->listable instanceof AuctionListing) {
            $specificRules = [
                'start_price' => 'required|numeric|min:0',
                'ends_at' => 'required|date',
                'reserve_price' => 'nullable|numeric|gte:start_price',
                'purchase_price' => 'nullable|numeric|gte:start_price',
            ];
        } elseif ($listing->listable instanceof DonationListing) {
            $specificRules = [
                'is_capped' => 'boolean',
            ];
        }

        // 4. --- Merge and return all rules ---
        return array_merge($rules, $specificRules, $mediaRules);
    }

    /**
     * Get the common listing data from the validated request.
     *
     * @return array
     */
    public function getCommonData(): array
    {
        return $this->safe()->only([
            'title',
            'description',
            'category_id',
        ]);
    }

    /**
     * Get the specific listable data from the validated request.
     *
     * @return array
     */
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
