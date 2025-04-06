<?php

namespace App\Http\Controllers\CustomerManagement;

use App\Http\Controllers\Controller;
use App\Models\CustomerManagement\Country;
use App\Models\CustomerManagement\CustomerClassification;
use App\Models\RefEnum\RefEnum;
use App\Models\Address\AddressCode;

class CustomerServiceController extends Controller
{
    public function refEnumForCustomer()
    {
        $individualCustomer = CustomerClassification::where('group_code', 'INDIVIDUAL')
            ->where('status', 'ACT')
            ->orderBy('description')
            ->pluck('description', 'cust_classification');
        $corporrateCustomer = CustomerClassification::where('group_code', 'CORPORATE')
            ->where('status', 'ACT')
            ->orderBy('description')
            ->pluck('description', 'cust_classification');
            
        $countries = $this->listCountries();
        $province = $this->getCityAndProvince();
        $commune = $this->getCommune();
        $district = $this->getDistrict();

        $customerType = RefEnum::where('group_id', 'CUSTOMER_TYPE')->orderBy('id', 'asc')->get()->pluck('name', 'enum_id');
        $contact = RefEnum::where('group_id', 'CUSTOMER_INFO')->where('type_id', 'CUSTOMER_CONTACT_LVL')->get()->pluck('enum_id', 'enum_id');
        $contactTypes = RefEnum::where('group_id', 'CUSTOMER_INFO')->where('type_id', 'CONTACT_TYPE')->get()->pluck('enum_id', 'enum_id');
        $data = array(
            'contactlvl' => $contact,
            'contacttype' => $contactTypes,
            'customertype' => $customerType,
            'customerIndi' => $individualCustomer,
            'customerCorp' => $corporrateCustomer,
            'countryOptions' => $countries,
            'provinceOptions' =>$province,
            'communeOptions' => $commune,
            'districtOptions' => $district
        );
        return $data;
    }

    private function listCountries() {
        return Country::select('country_code', 'description')
                ->where('status', 'ACT')
                ->orderBy('description')->get()->map(function($item) {
                    return [
                        'value' => $item->country_code,
                        'label' => $item->description
                    ];
                });
    }

    private function getCityAndProvince(){
        return AddressCode::select('province')->orderBy('province')->distinct()->get()->map(function($item){
            return [
                'value' => $item->province,
                'label' => $item->province
            ];
        });
    }
    private function getDistrict(){
        return AddressCode::select('province','district')->orderBy('district')->distinct('district')->get()->map(function($item){
            return [
                'province' => $item->province,
                'district' => $item->district
            ];
        });
    }
    private function getCommune(){
        return AddressCode::select('postal_code','district','commune')->orderBy('commune')->distinct('commune')->get()->map(function($item){
            return [
                'district' => $item->district,
                'commune' => $item->commune,
                'postal_code' => $item->postal_code
            ];
        });
    }
}
