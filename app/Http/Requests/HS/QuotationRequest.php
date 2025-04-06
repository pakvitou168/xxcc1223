<?php

namespace App\Http\Requests\HS;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuotationRequest extends FormRequest
{
    const JOINT = 'J';
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
            'file' => ['required', 'file'],
            'joint_status' => ['required'],
            'joint_details.*.customer_type' => [$this->requiredIfIsJoint()],
            'joint_details.*.customer_no' => [$this->requiredIfIsJoint()],
            'joint_details.*.joint_level' => [$this->requiredIfIsJoint()],
            'joint_details.*.permission' => [$this->requiredIfIsJoint()],
            'sale_channel' => ['required'],
            'business_code' => ['required'],
            'endorsement_clauses' => 'required',
            'general_exclusions' => 'required',
            'geographical_limit' => 'required',
            'policy_wording_version' => 'required',
            'handler_code' => 'required',
            'insured_name' => ['required'],
            'insured_name_kh' => ['required'],
        ];
    }

    private function requiredIfIsJoint() {
        return Rule::requiredIf(fn () => $this->joint_status === self::JOINT);
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
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'message' => 'Validation Failed',
            'errors' => $validator->errors(),
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
