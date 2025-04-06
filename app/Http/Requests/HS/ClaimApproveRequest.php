<?php

namespace App\Http\Requests\HS;

use Illuminate\Foundation\Http\FormRequest;

class ClaimApproveRequest extends FormRequest
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
            'status'    => ['required'],
            'comment'   => ['required']
        ];
    }

    public function messages()
    {
        return [
            'comment.required'   => 'Reason is required'
        ];
    }
}
