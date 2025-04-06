<?php

namespace App\Http\Requests\SecurityManagement;

 use App\Enums\RecordStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SecurityManagement\Fnc;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class FunctionRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules(Request $request) {
        return [
            'code' => ['required', 
                Rule::unique(Fnc::class, 'code')
                    ->ignore($request->id, 'id')
                ],
            'name' => 'required',
            'app_id' => 'required',
            'permissions' => 'required',
            'status' => ['required', Rule::enum(RecordStatus::class)]
        ];
    }

    public function messages() {
        return [
            'app_id.required' => 'The Application field is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'message' => 'Validation Failed',
            'errors' => $validator->errors(),
        ], 422);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
