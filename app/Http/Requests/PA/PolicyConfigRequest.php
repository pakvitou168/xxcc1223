<?php

namespace App\Http\Requests\PA;

use Illuminate\Foundation\Http\FormRequest;

class PolicyConfigRequest extends FormRequest
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
            'business_type' => ['required'],
            'policy_type' => ['required']
        ];
    }
}
