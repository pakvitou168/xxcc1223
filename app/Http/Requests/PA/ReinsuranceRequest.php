<?php

namespace App\Http\Requests\PA;

use Illuminate\Foundation\Http\FormRequest;

class ReinsuranceRequest extends FormRequest
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
            'reinsurances' => ['required', 'array'],
            'reinsurances.*.group_code' => ['required_if:reinsurances.*.id,null'],
            'reinsurances.*.treaty_code' => ['required_if:reinsurances.*.id,null'],

            'reinsurances.*.share' => ['required'],
            'reinsurances.*.tax_fee' => ['nullable'],
            'reinsurances.*.ri_commission' => ['nullable'],
            'reinsurances.*.deleted_at' => ['nullable'],
            'reinsurances.*.id' => ['nullable']
        ];
    }
    public function messages()
    {
        return [
            'reinsurances.*.group_code.required_if' => 'Partner group is required',
            'reinsurances.*.treaty_code.required_if' => 'Treaty code is required',
            'reinsurances.*.share.required' => 'Share is required',
            'reinsurances.*.tax_fee.required' => 'Tax & fee is required',
            'reinsurances.*.ri_commission.required' => 'Share is required',
        ];
    }
}
