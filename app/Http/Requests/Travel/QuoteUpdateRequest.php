<?php

namespace App\Http\Requests\Travel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class QuoteUpdateRequest extends FormRequest
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
            'product_code' => ['required'],
            'joint_status' => ['required'],
            'joint_details' => ['required_if:joint_status,==,J','array'],
            'joint_details.*.customer_type' => ['required_if:joint_status,==,J'],
            'joint_details.*.customer_no' => ['required_if:joint_status,==,J'],
            'joint_details.*.joint_level' => ['required_if:joint_status,==,J'],
            'joint_details.*.permission' => ['required_if:joint_status,==,J'],
            'sale_channel' => ['required'],
            'customer_type' => ['required'],
            'customer_no' => ['required'],
            'business_code' => ['required'],
            'endorsement_clauses' => ['required'],
            'general_exclusions' => ['required'],
            'coverage_id' => ['required'],
            'automatic_extensions' => ['required', 'array'],
            'accumulation_limit_amount' => ['required'],
            'policy_wording_version' => 'required',
            'handler_code' => ['required'],
            'insured_name' => ['required'],
            'insured_name_kh' => ['required'],
            'commission_rate' => ['nullable', 'max:100'],
            'surcharge' => ['nullable', 'max:100'],
            'discount' => ['nullable', 'max:100'],
            'insured_person_note' => ['nullable'],
            'insured_person_note_kh' => ['nullable'],
            'warranty' => ['nullable'],
            'warranty_kh' => ['nullable'],
            'memorandum' => ['nullable'],
            'memorandum_kh' => ['nullable'],
            'subjectivity' => ['nullable'],
            'subjectivity_kh' => ['nullable'],
            'remark' => ['nullable'],
            'remark_kh' => ['nullable'],
            'calc_option' => ['required'],
            'negotiation_rate' => ['required_if:calc_option,==,SPECIAL', 'max:100'],
            'insurance_period_type' => ['required'],
            'effective_date_from' => ['required'],
            'effective_date_to' => ['required'],
        ];
    }
    public function messages()
    {
        return [
            'file.required' => 'File is required',
            'file.file' => 'File is required',
            'joint_status.required' => 'Joint Status is required',
            'joint_details.*.customer_type.required' => 'Customer Type is required',
            'joint_details.*.customer_no.required' => 'Customer Name is required',
            'joint_details.*.joint_level.required' => 'Joint Level is required',
            'joint_details.*.permission.required' => 'Permission is required',
            'sale_channel.required' => 'Business Channel is required',
            'business_code.required' => 'Business Name is required',
            'endorsement_clauses.required' => 'Endorsement Clauses is required',
            'general_exclusions.required' => 'General Exclusions is required',
            'handler_code.required' => 'Business handler is required',
            'insured_name.required' => 'Insured name is required',
            'insured_name_kh.required' => 'Insured name(Khmer) is required',
            'optional_benefits.*.rating.required_with' => 'Rating is required',
            'optional_benefits.*.amount_type.required_with' => 'Amount type is required',
            'accumulation_limit_amount.required' => 'Accumulation limit is required'
        ];
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'message' => 'Validation Failed',
            'errors' => $validator->errors(),
        ], 422);
        throw new HttpResponseException($response);
    }
}
