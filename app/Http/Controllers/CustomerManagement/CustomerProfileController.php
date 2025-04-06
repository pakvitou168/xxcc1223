<?php

namespace App\Http\Controllers\CustomerManagement;

use App\Http\Controllers\Controller;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerManagement\CustomerProfileV;

class CustomerProfileController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->middleware('has-permission:CUSTOMER_PROFILE.VIEW')->only(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(CustomerProfileV::latest('id'));
    }

    public function show($customerProfileId,Request $request)
    {
        $customerProfile = CustomerProfileV::find($customerProfileId);
        $customerProfileSummary=null;
        $customerProfileList=[];
        if($request->underwrite_year){
            $params = [
                $customerProfile->customer_no,
                $request->underwrite_year
            ];
            $customerProfileSummary = collect(DB::select("select * from ins_generate_customer_profile_summary(?,?)", $params))->first();
            $customerProfileList = collect(DB::select("select * from ins_generate_customer_profile_policy_list(?,?)", $params))->map(function($item,$key){
                $item->no=$key+1;
                return $item;
            });
        }

        return [
            'customer_profile' => $customerProfile,
            'customer_profile_summary' => $customerProfileSummary,
            'customer_profile_list' => $customerProfileList
        ];
    }
}
