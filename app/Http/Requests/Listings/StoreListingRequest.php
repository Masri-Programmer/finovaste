<?php

namespace App\Http\Requests\Listings;

use Carbon\Carbon;
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
        return true;
    }

    protected function prepareForValidation(): void
    {
        $parseDate = function ($dateInput) {
            if (is_array($dateInput) && !empty($dateInput)) {
                if (isset($dateInput['year']) && isset($dateInput['month']) && isset($dateInput['day'])) {
                    return "{$dateInput['year']}-{$dateInput['month']}-{$dateInput['day']}";
                }

                if (isset($dateInput[0]) && is_string($dateInput[0])) {
                    return $dateInput[0];
                }
            }

            if (is_string($dateInput)) {
                return $dateInput;
            }

            return null;
        };

        $expires = $parseDate($this->expires_at);
        $starts  = $parseDate($this->starts_at);
        $ends    = $parseDate($this->ends_at);

        $this->merge([
            'expires_at' => $expires ? Carbon::parse($expires)->toDateTimeString() : null,
            'starts_at'  => $starts  ? Carbon::parse($starts)->toDateTimeString() : null,
            'ends_at'    => $ends    ? Carbon::parse($ends)->toDateTimeString() : null,
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $fallbackLocale = config('app.fallback_locale');
        $rules = [
            // Common Data
            'title' => 'required|array',
            'title.' . $fallbackLocale => 'required|string|max:255',
            'title.*' => 'nullable|string|max:255',
            'description' => 'required|array',
            'description.' . $fallbackLocale => 'required|string',
            'description.*' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'listing_type' => 'required|string|in:auction,donation,purchase,investment',
            'location_text' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',

            // Media Data
            'images'   => 'nullable|array',
            'images.*' => 'file|mimetypes:image/jpeg,image/png,image/webp|max:10240',
            'documents'   => 'nullable|array',
            'documents.*' => 'file|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:5120',
            'videos'   => 'nullable|array',
            'videos.*' => 'file|mimetypes:video/mp4,video/quicktime|max:51200',
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
                    'starts_at' => 'nullable|date|after:now',
                    'ends_at' => 'required|date|after:starts_at',
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

            case 'purchase':
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
            'location_text',
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
                'starts_at',
                'ends_at',
                'reserve_price',
                'buy_it_now_price'
            ]),
            'donation' => $this->safe()->only([
                'donation_goal',
                'is_goal_flexible'
            ]),
            'purchase' => $this->safe()->only([
                'price',
                'quantity',
                'condition'
            ]),
            default => [],
        };
    }
}
