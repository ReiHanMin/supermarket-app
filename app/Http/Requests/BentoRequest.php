<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class BentoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
{
    return [
        'bentos.*.name' => ['required', 'max:2000'],
        'bentos.*.original_price' => ['required', 'numeric', 'min:0.01'],
        'bentos.*.usual_discounted_price' => ['nullable', 'numeric'],
        'bentos.*.discount_percentage' => ['nullable', 'numeric'],
        'bentos.*.stock_message' => ['nullable', 'string'],
        'bentos.*.calories' => ['nullable', 'numeric'],
        'bentos.*.description' => ['nullable', 'string'],
        'bentos.*.availability' => ['required', 'string'],
        'bentos.*.store_id' => ['required', 'exists:stores,id'],
        'bentos.*.image_url' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
    ];
}


    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        // Log validation errors to help with debugging
        \Log::error('Validation failed:', $validator->errors()->toArray());

        // Throw the validation exception with the validation errors
        throw new ValidationException($validator);
    }
}
