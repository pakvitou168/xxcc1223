<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecoveryRequest extends FormRequest
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
            'cause_of_losses' => 'required',
            'cause_of_losses.*.deductible_paid' => 'required|numeric|gte:0',
            'cause_of_losses.*.insured_sharing_request' => 'required|numeric|gte:0',
            'cause_of_losses.*.payment_type' => 'required',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'cause_of_losses.required'=>'The type of loss field is required.',
            'cause_of_losses.*.deductible_paid.required' => 'Deductible paid is required.',
            'cause_of_losses.*.deductible_paid.numeric' => 'Deductible paid need to be number.',
            'cause_of_losses.*.deductible_paid.gte' => 'Deductible paid must be greater than or equal 0.',
            'cause_of_losses.*.insured_sharing_request.required' => 'Insured sharing request is required.',
            'cause_of_losses.*.insured_sharing_request.numeric' => 'Insured sharing request need to be number.',
            'cause_of_losses.*.insured_sharing_request.gte' => 'Insured sharing request must be greater than or equal 0.',
            'cause_of_losses.*.payment_type.required' => 'Payment type is required.',
        ];
    }
}
