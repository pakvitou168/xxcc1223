<?php

namespace App\Http\Requests;

use App\Models\Claim\Process\ClaimTransaction;
use App\Services\Claim\PartialPaymentService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessRequest extends FormRequest
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
        $currentTransaction = ClaimTransaction::find($this->id);
        return [
            'claim_no' => [
                'required',
                Rule::unique(ClaimTransaction::class, 'claim_no')
                    ->ignore($currentTransaction)
                    ->where('status', 'ACT'),
                fn ($attribute, $value, $fail) => $this->hasNoPendingPartialPayment($attribute, $value, $fail),
            ],
            'cause_of_losses' => 'required',
            'cause_of_losses.*.cause_of_loss_code' => 'required',
            'cause_of_losses.*.payee_id' => 'required',
            'cause_of_losses.*.remain_amount' => 'required',
            'cause_of_losses.*.payment_type'=>'required',
            'cause_of_losses.*.recovery_from_third_party' => 'nullable|numeric|gte:0|lte:cause_of_losses.*.remain_amount',
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
            'cause_of_losses.*.cause_of_loss_code.required' => 'The type of loss field is required.',
            'cause_of_losses.*.payee_id.required' => 'The payee field is required.',
            'cause_of_losses.*.remain_amount.required' => 'The payable amount field is required.',
            'cause_of_losses.*.payment_type.required' => 'The payment type field is required.',
            'cause_of_losses.*.recovery_from_third_party.numeric' => 'Recovery from Third Party need to be number.',
            'cause_of_losses.*.recovery_from_third_party.gte' => 'Recovery from Third Party must be greater or equals to 0',
            'cause_of_losses.*.recovery_from_third_party.lte' => 'Recovery from Third Party must be less or equals to Remain amount',
        ];
    }

    private function hasNoPendingPartialPayment($attribute, $value, $fail) {
        $passed = (new PartialPaymentService)->hasNoPendingPartialPayment($this->claim_no);
        
        if (!$passed) $fail('Pending Partial Payment, please resolve it to continue.');
    }
}
