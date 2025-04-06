<?php

namespace App\Http\Requests;

use App\Models\Cover\Cover;
use App\Models\CoverPackage\CoverPackageComponent;
use App\Models\Product\Product;
use Illuminate\Foundation\Http\FormRequest;

class AutoDetailRequest extends FormRequest
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
            'make' => 'required',
            'model' => 'required',
            'vehicle_value' => $this->checkIfCoversNeedVehicleValues($this) ? 'required|numeric|gt:0' : 'required|numeric|min:0',
            'manufacturing_year' => 'required|numeric|digits:4',
            'surcharge' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'ncd' => 'nullable|numeric',
            'passenger_tonnage' => $this->hasPassengerTonnage($this->product_code) ? 'required|numeric|min:0' : '',
            'vehicle_usage' => 'required',
        ];
    }

    private function checkIfCoversNeedVehicleValues($request) {
        $covers = [];

        if (!$request->cover_pkg_id) {
            $covers = $request->optional_covers;
        } else {
            $packageCovers = CoverPackageComponent::where('cpkg_id', $request->cover_pkg_id)
                ->where('status', 'ACT')
                ->pluck('comp_code');
            $covers = $packageCovers->merge($request->optional_covers)->unique()->values();
        }

        $requiredVehicleValueArr = Cover::whereIn('code', $covers)
            ->where('product_code', $request->product_code)
            ->where('status', 'ACT')
            ->pluck('is_required_vehicle_val')
            ->toArray();

        if (in_array(true, $requiredVehicleValueArr)) return true;
        
        return false;
    }
    
    private function hasPassengerTonnage($productCode) {
        $productSpecification = Product::getProductSpecificationByCode($productCode);

        return $productSpecification === 'TONNAGE' || $productSpecification === 'PASSENGER';
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'make.required' => 'Make is required.',
            'model.required' => 'Model is required.',
            'vehicle_value.required' => 'Value of Vehicles is required.',
            'vehicle_value.min' => 'Value of Vehicles must be at least 0.',
            'manufacturing_year.required' => 'Year of Manufacture is required.',
            'manufacturing_year.digits' => 'Year of Manufacture is not a valid date.',
            'surcharge:min' => 'Surcharge must be at least 0.',
            'discount:min' => 'Discount must be at least 0.',
            'passenger_tonnage:required' => 'Passenger / Tonnage is required.',
            'passenger_tonnage:min' => 'Passenger / Tonnage must be at least 0.',
            'vehicle_usage:required' => 'Vehicle Usage is required.'
        ];
    }
}
