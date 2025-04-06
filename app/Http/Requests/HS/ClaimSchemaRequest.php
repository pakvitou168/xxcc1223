<?php

namespace App\Http\Requests\HS;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClaimSchemaRequest extends FormRequest
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
            'policy_id' => ['required'],
            'claim_id' => ['required'],
            'date_of_disability' => ['required'],
            'date_of_completed_doc' => ['required'],

            'schema_data.*.number_of_day' =>
                [
                    fn($attribute, $value, $fail) => $this->isExpenseValid($attribute, $value, $fail)
                ],
            'schema_data.*.actual_incurred_expense' =>
                [
                    fn($attribute, $value, $fail) => $this->isSchemaUpdate($attribute, $value, $fail)
                ],
            'schema_data.*.id' => ['nullable'],
            'schema_data.*.schema_name' => ['required'],
            'schema_data.*.admission_date' => ['nullable'],
            'schema_data.*.discharge_date' => ['nullable'],
            'schema_data.*.limit_amount' => ['required'],
            'schema_data.*.maximum_payable' => ['nullable'],
            'schema_data.*.max_number_of_day' => ['nullable'],
            'schema_data.*.non_payable_expense' => ['nullable'],
            'schema_data.*.schema_detail_code' => ['nullable'],
            'schema_data.*.schema_id' => ['nullable'],

            'non_payable_expense' => ['nullable'],
            'total_actual_incurred_expense' => ['required'],
            'total_maximum_payable' => ['required'],
            'total_non_payable_expense' => ['required'],
            'previous_payment' => ['nullable'],
            'total_amount_due' => ['nullable'],
            'due_to' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'schema_date.*.number_of_day.required' => 'Number of day is required'
        ];
    }

    private function isExpenseValid($attribute, $value, $fail)
    {
        $index = explode('.', $attribute)[1];
        if ($this->schema_data[$index]['actual_incurred_expense'] && $this->schema_data[$index]['max_number_of_day'] && is_null($value)) {
            $fail("Number of day is required");
        }
    }
    private function isSchemaUpdate($attribute, $value, $fail)
    {
        $index = explode('.', $attribute)[1];
        if (!is_null($this->schema_data[$index]['schema_id']) && is_null($value)) {
            $fail("Actual incurred expense is required");
        }
    }
}
