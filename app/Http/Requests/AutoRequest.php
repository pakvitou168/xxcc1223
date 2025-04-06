<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutoRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'product_code' => 'required',
            'calc_option' => 'required',
            'negotiation_rate' => 'required_if:calc_option,SPECIAL',
            'customer_type' => 'required',
            'customer_no' => 'required',
            'joint_status' => 'required',
            'joint_details.*.customer_type' => 'required',
            'joint_details.*.customer_no' => 'required',
            'joint_details.*.joint_level' => 'required',
            'joint_details.*.permission' => 'required',
            'insured_name' => 'required',
            'insured_name_kh' => 'required_unless:customer_type,CA',
            'insurance_period_type' => 'required',
            'effective_date_from' => 'required',
            'effective_date_to' => 'required',
            'endorsement_clause' => 'required',
            'general_exclusive' => 'required',
            'sale_channel' => 'required',
            'business_code' => 'required',
            'handler_code' => 'required',
            'vehicles.*.make' => 'required',
            'vehicles.*.model' => 'required',
            'vehicles.*.vehicle_value' => 'required',
            'vehicles.*.manufacturing_year' => 'required'
        ];
    }
    public function messages(){
        return [
            'joint_details.*.customer_type' => 'customer type is required',
            'joint_details.*.customer_no' => 'customer no is required',
            'joint_details.*.joint_level' => 'joint level is required',
            'joint_details.*.permission' => 'permission is required',
        ];
    }
}
