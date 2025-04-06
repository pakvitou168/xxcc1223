<?php

namespace App\Http\Requests\Travel;

use App\Models\Travel\WorkingClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InsuredPersonRequest extends FormRequest
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
            'master_data_type' => ['required'],
            'product_code' => ['required'],
            'master_data_id' => ['required'],
            'name' => ['required'],
            'occupation' => ['required'],
            'date_of_birth' => ['nullable'],
            'gender' => ['nullable'],
            'working_class_code' => ['required', Rule::in(WorkingClass::codeList())],
            'relationship' => ['nullable'],
            'sum_insured' => ['required', 'numeric'],
            'permanent_disablement_amount' => ['required', 'numeric'],
            'medical_expense_amount' => ['required', 'numeric'],
        ];
    }
    public function messages()
    {
        return [
            'product_code.required' => 'No product code found',
            'master_data_id.required' => 'Quote not found'
        ];
    }
}
