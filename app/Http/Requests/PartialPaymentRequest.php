<?php

namespace App\Http\Requests;

use App\Models\Claim\Process\ClaimTransaction;
use App\Services\Claim\PartialPaymentService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartialPaymentRequest extends FormRequest
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
            'claim_no' => [
                'required',
                // Exclude the next rule, if it is an update request
                Rule::excludeIf(fn() => $this->id),
                fn ($attribute, $value, $fail) => $this->hasNoPendingPartialPayment($attribute, $value, $fail),
                fn ($attribute, $value, $fail) => $this->alreadyHasFullPayment($attribute, $value, $fail),
            ],
            'cause_of_losses.*.payee_id' => 'required',
            'cause_of_losses' => 'required',
            'cause_of_losses.*.amount' => [
                'required',
                'numeric',
                'gt:0',
                fn ($attribute, $value, $fail) => $this->notHigherThanEstimated($attribute, $value, $fail)
            ],
            'cause_of_losses.*.payment_type' =>'required'
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
            'claim_no.required'=> 'Claim No. is required.',
            'cause_of_losses.required'=> 'Cause of Losses is required.',
            'cause_of_losses.*.amount.required' => 'Amount is required.',
            'cause_of_losses.*.amount.numeric' => 'Amount need to be number.',
            'cause_of_losses.*.amount.gt' => 'Amount must be greater than 0.',
            'cause_of_losses.*.payee_id.required' => 'Payee is required.',
            'cause_of_losses.*.payment_type.required' => 'Payment Type is required.',
        ];
    }

    private function notHigherThanEstimated($attribute, $value, $fail) {

        $index = explode('.', $attribute)[1];        
        $causeCode = $this->cause_of_losses[$index]['cause_of_loss_code'];
        $condType = $this->id ? 'UPDATE' : 'NEW';
        $passed = (new PartialPaymentService)->notHigherAmountThanEstimated($this->claim_no, $causeCode, $value, $condType);

        if (!$passed) $fail('Amount has exceeded the estimated amount.');
    }

    private function hasNoPendingPartialPayment($attribute, $value, $fail) {
        $passed = (new PartialPaymentService)->hasNoPendingPartialPayment($this->claim_no);
        
        if (!$passed) $fail('Pending Partial Payment, approval is required.');
    }

    private function alreadyHasFullPayment($attribute, $value, $fail) {
        $passed = ClaimTransaction::where('claim_no', $this->claim_no)->doesntExist();

        if (!$passed) $fail('Already processed to full payment, Cannot create partial payment.');
    }
}
