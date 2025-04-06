<?php

namespace App\Http\Controllers\CustomerManagement;

use App\Http\Controllers\Controller;
use App\Models\CustomerManagement\Customer;
use App\Models\CustomerManagement\CustomerContact;
use App\Models\CustomerManagement\CustomerCorporate;
use App\Models\CustomerManagement\CustomerIndividual;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Address\AddressCode;
use App\Models\CustomerManagement\Country;

class CustomerController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Customer::class, 'customer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(Customer::where('status', 'ACT')->latest('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customerNo = collect(DB::select('select * from ins_generate_customer_no() as customer_no'))->first()->customer_no;
        $customer = new Customer;

        $customer->customer_no = $customerNo;
        $this->assignValues($request, $customer);

        if ($customer->save()) {
            $this->storeCustomerDetail($request, $customer);
            $this->storeCustomerContact($request, $customer);

            return response()->json('Record is created.', 201);
        }
    }

    private function assignValues($request, $customer) {
        $customer->broker_id = $request->broker_id;
        $customer->customer_type = $request->customer_type;
        $customer->name_kh = $request->name_kh;
        $customer->name_en = strtoupper($request->name_en);
        $customer->language_code =$request->language_code;
        $customer->cust_classification = $request->cust_classification;
        $customer->risk_category = $request->risk_category;
        $customer->address_en = $request->address_en;
        $customer->village_en = $request->village_en;
        $customer->postal_code = $request->postal_code;
        $customer->country_code = $request->country_code;
    }

    private function storeCustomerDetail($request, $customer) {
        if ($request->customer_type == 'IC') {
            $customerIndividual = CustomerIndividual::firstOrCreate(['customer_no' => $customer->customer_no]);
            $customerIndividual->customer_no = $customer->customer_no;
            $customerIndividual->date_of_birth = $request->date_of_birth;
            $customerIndividual->gender = $request->gender;
            $customerIndividual->identity_type = $request->identity_type;
            $customerIndividual->identity_no = $request->identity_no;
            $customerIndividual->national = $request->national;
            $customerIndividual->nationality = $request->nationality;
            $customerIndividual->identity_iss_date = $request->identity_iss_date;
            $customerIndividual->identity_exp_date = $request->identity_exp_date;

            return $customerIndividual->save();
        } else {
            $customerCorp = CustomerCorporate::firstOrCreate(['customer_no' => $customer->customer_no]);

            $customerCorp->customer_no = $customer->customer_no;
            $customerCorp->incorporate_date = $request->incorporate_date;
            $customerCorp->business_registration_no = $request->business_registration_no;

            if ($request->customer_type== 'CL') {
                $customerCorp->tin_code = $request->tin_code;
            } else {
                $customerCorp->foreign_tin_no = $request->tin_code;
            }
            return $customerCorp->save();
        }
    }

    private function storeCustomerContact($request, $customer) {
        $customer->customerContacts()->delete();

        if ($request->contactgroup) {
            $contacts = collect($request->contactgroup)->map(function($item) use ($customer) {
                $item = (object) $item;

                return [
                    'customer_no' => $customer->customer_no,
                    'contact_level' => $item->contact_level,
                    'contact_type' => $item->contact_type,
                    'contact_info' => $item->contact_info,
                ];
            });
            return $customer->customerContacts()->createMany($contacts);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\customerManagement\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $customer->occupation_descritpion = Customer::getCustomerClassificationDesc($customer->cust_classification);
        $customerType = '';
        if ($customer->customer_type == "IC") {
            $customerType = CustomerIndividual::where('customer_no', $customer->customer_no)->get();
        } else {
            $customerType = CustomerCorporate::where('customer_no', $customer->customer_no)->get();
        }
        $customerContact = CustomerContact::where('customer_no', $customer->customer_no)->get();
        $address = AddressCode::where('postal_code', $customer->postal_code)->first();
        $country = Country::select('description')->where('country_code', $customer->country_code)->value('description');
        $data = array(
                    'customer' => $customer,
                    'customerType' => $customerType,
                    'customerContact' => $customerContact,
                    'address' => $address,
                    'country' => $country
                );
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\customerManagement\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        $customerOption = '';
        if ($customer->customer_type == "IC") {
            $customerOption = CustomerIndividual::where('customer_no', $customer->customer_no)->first();
        } else {
            $customerOption = CustomerCorporate::where('customer_no', $customer->customer_no)->first();
        }

        $cantactGroup = CustomerContact::where('customer_no', $customer->customer_no)->get(['contact_level', 'contact_type', 'contact_info', 'id']);

        $addressData = AddressCode::select('province', 'district', 'commune')->where('postal_code', $customer->postal_code)->first();

        $dataArr = array(
                'customer' => $customer,
                'customerOption' => $customerOption,
                'contactGroup' => $cantactGroup,
                'addressData' => $addressData
            );
        return $dataArr;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\customerManagement\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $this->assignValues($request, $customer);

        if ($customer->save()) {
            $this->storeCustomerDetail($request, $customer);
            $this->storeCustomerContact($request, $customer);

            return response()->json('Record is updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\customerManagement\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->status = "DEL";

        if ($customer->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
