<?php

namespace App\Http\Requests\SecurityManagement;

 use App\Enums\RecordStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SecurityManagement\Branch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class BranchRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules(Request $request) {
        return [
            'code' => ['required', 
                Rule::unique(Branch::class)
                    ->where(fn($query) => $query->where('org_id', $request->org_id))
                    ->ignore($request->id, 'id')
                ],
            'name' => 'required',
            'org_id' => 'required',
            'status' => ['required', Rule::enum(RecordStatus::class)]
        ];
    }

    public function messages() {
        return [
            'org_id.required' => 'The Organization field is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response_format($validator->errors(), 'Validation Failed', false, 422);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
