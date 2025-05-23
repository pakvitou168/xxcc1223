<?php

namespace App\Http\Requests\SecurityManagement;

use App\Models\SecurityManagement\Role;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Request;

class RoleRequest extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            'name' => 'required',
            'code' => [
                'required',
                Rule::unique(Role::class, 'code')->ignore($request->id, 'id'),
            ],
            'status' => 'required',
        ];
    }

    protected function failedValidation(Validator $validator) {
        $response = response_format($validator->errors(), 'Validation Failed', false, 422);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
