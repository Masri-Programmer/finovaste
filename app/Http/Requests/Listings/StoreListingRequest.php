<?php

namespace App\Http\Requests\Listings;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Your route already protects this with 'auth' middleware,
        // so we can simply return true here.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            // Common Data
            'title' => 'required|array',
            'title.*' => 'nullable|string|max:255',
            'description' => 'required|array',
            'description.*' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'listing_type' => 'required|string|in:auction,donation,buy_now,investment',
            'expires_at' => 'nullable|date|after:now',

            // Media Data
            'images'   => 'nullable|array',
            'images.*' => 'file|mimetypes:image/jpeg,image/png,image/webp',
            'documents'   => 'nullable|array',
            'documents.*' => 'file|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'videos'   => 'nullable|array',
            'videos.*' => 'file|mimetypes:video/mp4,video/quicktime',
        ];

        // 2. --- Type-Specific Rules ---
        $specificRules = [];
        switch ($this->input('listing_type')) {

            case 'investment':
                $specificRules = [
                    'investment_goal' => 'required|numeric|min:0',
                    'minimum_investment' => 'required|numeric|min:0',
                    'shares_offered' => 'required|integer|min:1',
                    'share_price' => 'required|numeric|min:0',
                ];
                break;

            case 'auction':
                $specificRules = [
                    'start_price' => 'required|numeric|min:0',
                    'ends_at' => 'required|date|after:now',
                    'reserve_price' => 'nullable|numeric|gte:start_price',
                    'buy_it_now_price' => 'nullable|numeric|gte:start_price',
                ];
                break;

            case 'donation':
                $specificRules = [
                    'donation_goal' => 'required|numeric|min:0',
                    'is_goal_flexible' => 'boolean',
                ];
                break;

            case 'buy_now':
                $specificRules = [
                    'price' => 'required|numeric|min:0',
                    'quantity' => 'required|integer|min:1',
                    'condition' => 'nullable|string|max:100',
                ];
                break;
        }

        return array_merge($rules, $specificRules);
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
            'listing_type',
            'expires_at',
        ]);
    }

    /**
     * Get the specific listable data from the validated request.
     *
     * @return array
     */
    public function getSpecificData(): array
    {
        $type = $this->input('listing_type');

        return match ($type) {
            'investment' => $this->safe()->only([
                'investment_goal',
                'minimum_investment',
                'shares_offered',
                'share_price'
            ]),
            'auction' => $this->safe()->only([
                'start_price',
                'ends_at',
                'reserve_price',
                'buy_it_now_price'
            ]),
            'donation' => $this->safe()->only([
                'donation_goal',
                'is_goal_flexible'
            ]),
            'buy_now' => $this->safe()->only([
                'price',
                'quantity',
                'condition'
            ]),
            default => [],
        };
    }
}
