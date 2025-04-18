<?php

namespace App\Http\Requests\PA;

use App\Services\PA\BaseService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EndorsementRequest extends FormRequest
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
            'endorsement_type' => ['required',Rule::in(app(BaseService::class)->endorsementOptions()->pluck('value')->toArray())],
            'endorsement_effective_date' => ['required'],
            'endorsement_description' => ['nullable']
        ];
    }
}
