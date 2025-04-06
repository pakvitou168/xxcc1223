<?php

namespace App\Http\Requests\PA;

use Illuminate\Foundation\Http\FormRequest;

class CommissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'premium_tax_fee_rate' => ['nullable', 'numeric'],
            'commission_rate' => ['nullable', 'numeric'],
            'witholding_tax_rate' => ['nullable', 'numeric']
        ];
    }
}
