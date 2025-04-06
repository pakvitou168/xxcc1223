<?php

namespace App\Http\Requests\SecurityManagement;

 use App\Enums\RecordStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SecurityManagement\Group;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class GroupRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules(Request $request) {
        return [
            'code' => ['required', 
                Rule::unique(Group::class)
                    ->ignore($request->id, 'id')
                ],
            'name' => 'required',
            'is_default' => 'boolean',
            'status' => ['required', Rule::enum(RecordStatus::class)]
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
