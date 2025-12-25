<?php

namespace App\Http\Requests\Listings;

use Carbon\Carbon;use App\Http\Requests\BaseFormRequest;

class StoreListingRequest extends BaseFormRequest
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
        $userLocale = app()->getLocale();
        $rules = [
            // Common Data
            'title' => 'required|array',
            'title.' . $userLocale => 'required|string|max:255',
            'title.*' => 'nullable|string|max:255',
            'description' => 'required|array',
            'description.' . $userLocale => 'required|string',
            'description.*' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|string|in:private,creative,charity,charity_auction,auction,donation', // Support legacy types for now too if needed, or just new ones
            'mode' => 'nullable|string|in:purchase,auction,donation',
            'listing_type' => 'nullable|string', // Deprecated but kept for backward compatibility if needed
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
        
        $type = $this->input('type') ?? $this->input('listing_type');
        $mode = $this->input('mode') ?? 'donation'; // Default assumption for validation logic if not set

        // Infer mode for validation if not explicitly set
        if (in_array($type, ['private', 'creative', 'charity'])) {
            $mode = 'donation';
        } elseif ($type === 'purchase') {
            $mode = 'purchase';
        } elseif ($type === 'auction') {
            $mode = 'auction';
        } elseif ($type === 'charity_auction') {
             // validation depends on chosen mode (auction or purchase)
             // If user selects charity_auction, they MUST send a mode
             // If they don't, we might have issues. Let's rely on input mode.
        }

        // Use mode for validation
        switch ($mode) {

        

            case 'auction':
                $specificRules = [
                    'start_price' => 'required|numeric|min:0',
                    'starts_at' => 'nullable|date|after:now',
                    'ends_at' => 'required|date|after:starts_at',
                    'reserve_price' => 'nullable|numeric|gte:start_price',
                    'purchase_price' => 'nullable|numeric|gte:start_price',
                ];
                break;

            case 'donation':
                $specificRules = [
                    'target' => 'required|numeric|min:0',
                    'is_capped' => 'boolean',
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
            'type',
            'mode',
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
        $type = $this->input('type');
        $mode = $this->input('mode');
        
        // Map abstract types to modes if needed
        if (in_array($type, ['private', 'creative', 'charity'])) {
            $mode = 'donation';
        }

        return match ($mode) {
           
            'auction' => $this->safe()->only([
                'start_price',
                'starts_at',
                'ends_at',
                'reserve_price',
                'purchase_price'
            ]),
            'donation' => $this->safe()->only([
                'target',
                'is_capped'
            ]),
         
            default => [],
        };
    }
}
