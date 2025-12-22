<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\HasAppMessages;

class BaseFormRequest extends FormRequest
{
    use HasAppMessages;

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        // 1. Get the first error message to show in the toast notification
        $firstError = $validator->errors()->first();

        // 2. Use your existing trait to generate the notification response
        // We pass the first validation error as the message
        $response = $this->checkError($firstError, null, [
            // 'title' => 'Validation Error' // Optional: Override title if needed
        ]);

        // 3. IMPORTANT: Chain standard form error handling
        // We must manually add the errors and input back so standard form 
        // highlighting (red borders) still works in your views.
        $response->withErrors($validator)->withInput();

        // 4. Throw the exception with our custom response
        throw new HttpResponseException($response);
    }
}