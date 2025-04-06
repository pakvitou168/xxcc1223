<?php

namespace App\Http\Requests\Travel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class OptionalExtRequest extends FormRequest
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
            'optional_benefits' => ['required','array'],
            'optional_benefits.*.extension_name' => ['required'],
            'optional_benefits.*.extension_description' => ['required'],
            'optional_benefits.*.extension_id' => ['required'],
            'optional_benefits.*.extension_code' => ['required'],
            'optional_benefits.*.is_selected' => ['required'],
            'optional_benefits.*.amount_type' => ['required_if:optional_benefits.*.is_selected,==,true'],
            'optional_benefits.*.rating' => ['required_if:optional_benefits.*.is_selected,==,true']
        ];
    }

    public function messages()
    {
        return [
            'optional_benefits.*.rating.required_if' => 'Rating is required',
            'optional_benefits.*.amount_type.required_if' => 'Amount type is required',
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
