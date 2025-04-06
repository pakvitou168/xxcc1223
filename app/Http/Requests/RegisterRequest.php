<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'policy_no' => 'required',
            'detail_id' => 'required',
            'cause_of_losses' => 'required',
            'cause_of_losses.*.code' => 'required|distinct',
            'cause_of_losses.*.name' => 'required',
            'cause_of_losses.*.value' => 'required|numeric|gt:0',
            'cause_of_losses.*.recovery_from_third_party' => 'nullable|numeric|gte:0|lte:cause_of_losses.*.value',
            // 'third_party_id' => 'required',
            'notification_date' => 'required',
            'incident_date' => 'required',
            'incident_location' => 'required',
            'driver_id' => 'required',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'cause_of_losses.required'=>'The type of loss field is required.',
            'cause_of_losses.*.code.required' => 'The type of loss field is required.',
            'cause_of_losses.*.code.distinct' => 'The type of loss field has a duplicate value.',
            'cause_of_losses.*.value.required' => 'The value field is required.',
            'cause_of_losses.*.value.numeric' => 'The value field need to be number.',
            'cause_of_losses.*.value.gt' => 'The value must be greater than 0.',
            'cause_of_losses.*.recovery_from_third_party.numeric' => 'Recovery from Third Party need to be number.',
            'cause_of_losses.*.recovery_from_third_party.gte' => 'Recovery from Third Party must be greater or equals to 0',
            'cause_of_losses.*.recovery_from_third_party.lte' => 'Recovery from Third Party must be less or equals to Reserve amount',
        ];
    }
}
