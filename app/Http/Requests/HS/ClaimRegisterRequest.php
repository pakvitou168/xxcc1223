<?php

namespace App\Http\Requests\HS;

use App\Models\HS\Claim\ClaimRegister;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ClaimRegisterRequest extends FormRequest
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
            'policy_id'         => ['required'],
            'data_detail_id'    => ['required'],
            'cause_of_loss_disability'      => ['required'],
            'cause_of_loss'     => ['required'],
            'date_of_loss'      => ['required'],
            'notification_date' => ['required'],
            'clinic_id'         => ['required'],
            'reserve_amount'    => ['required','numeric'],
            'location_of_loss'  => ['required'],
            'loss_description'  => ['required','string', 'max:500'],
            'schema_plan'       => ['required'],
            'schema_type'       => ['required'],
            'schema_detail_code'=> ['nullable']
        ];
    }

    public function messages()
    {
        return [
            'policy_id.required'  => 'Policy no. is required',
            'data_detail_id.required'  => 'Insured person is required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'message'   => 'Validation Failed',
                'errors'    => $validator->errors(),
            ], 422);
        }
        throw new ValidationException($validator, $response);
    }
}
