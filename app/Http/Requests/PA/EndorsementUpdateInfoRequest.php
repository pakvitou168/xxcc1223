<?php

namespace App\Http\Requests\PA;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EndorsementUpdateInfoRequest extends FormRequest
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
            'insured_name' => ['required'],
            'insured_name_kh' => ['required'],
            'endorsement_clauses' => ['required', 'array'],
            'general_exclusions' => ['required'],
            'coverage_id' => ['required'],
            'policy_wording_version' => ['required'],
            'automatic_extensions' => ['required'],
            'accumulation_limit_amount' => ['required'],
            'sale_channel' => ['required'],
            'business_code' => ['required'],
            'handler_code' => ['required'],
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
        ];
    }
    public function messages(){
        return [
            'coverage_id.required' => 'Geographical limit is required'
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
