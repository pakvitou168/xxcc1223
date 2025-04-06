<?php

namespace App\Http\Requests\SecurityManagement;

use App\Enums\SMUser;
 use App\Enums\RecordStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SecurityManagement\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules(Request $request) {
        $request['password'] = $request->authenticator !==  SMUser::LDAP->value ? $request->password : null;
        $request['password_confirmation'] = $request->authenticator !==  SMUser::LDAP->value ? $request->password_confirmation : null;

        $passwordValidation = [
            Rule::requiredIf($request->authenticator !==  SMUser::LDAP->value),
            $request->authenticator != SMUser::LDAP->value ? 'confirmed' : '',
            $request->authenticator != SMUser::LDAP->value ? 'min:6' : '',
            $request->authenticator != SMUser::LDAP->value ? 'max:12' : '',
        ];

        $passwordConfirmationValidation = [
            Rule::requiredIf($request->authenticator != SMUser::LDAP->value)
        ];

        if(!$request['password'] && $request->id){
            $passwordValidation = ['nullable'];
            $passwordConfirmationValidation = ['nullable'];
        }

        return [
            'username' => ['required', 
                Rule::unique(User::class)
                    ->ignore($request->id, 'id')
                ],
            'email' => ['required', 'email',
                Rule::unique(User::class)
                    ->ignore($request->id, 'id')
                ],
            'full_name' => 'required',
            'password' => $passwordValidation,
            'password_confirmation' => $passwordConfirmationValidation,
            'authenticator' => 'nullable',
            'status' => ['required', Rule::enum(RecordStatus::class)],
            'home_branch' => 'nullable'
        ];
    }

    public function messages() {
        return [
            'password.confirmed' => 'The Confirm Password field does not match.',
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
