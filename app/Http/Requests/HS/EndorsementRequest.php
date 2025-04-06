<?php

namespace App\Http\Requests\HS;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class EndorsementRequest extends FormRequest
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
            'insured_name'          => ['required'],
            'insured_name_kh'       => ['required'],
            'geographical_limit'    => ['required'],
            'endorsement_clauses'   => ['required', 'array'],
            'endorsement_clauses.*' => ['required'],
            'general_exclusions'    => ['required', 'array'],
            'general_exclusions.*'  => ['required']
        ];
    }

    public function messages()
    {
        return [
            'insured_name.required'         => 'Insured Name is required',
            'insured_name_kh.required'      => 'Insured Name Khmer is required',
            'endorsement_clauses.required'  => 'Endorsement Clauses is required',
            'endorsement_clauses.*.required' => 'Endorsement Clauses is required',
            'endorsement_clauses.array'     => 'Endorsement Clauses must be array',
            'general_exclusions.required'   => 'General Exclusions is required',
            'general_exclusions.*.required' => 'General Exclusions is required',
            'general_exclusions.array'      => 'General Exclusions must be array',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
