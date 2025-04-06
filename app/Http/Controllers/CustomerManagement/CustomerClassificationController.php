<?php

namespace App\Http\Controllers\CustomerManagement;

use App\Http\Controllers\Controller;
use App\Models\CustomerManagement\CustomerClassification;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerClassificationController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(CustomerClassification::class, 'customer_classification');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            CustomerClassification::where('status', 'ACT')
                ->latest()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        $customerClassification = new CustomerClassification();

        $customerClassification->cust_classification = $this->generateCustClassification($request->post('group_code'));
        $this->assignValues($request, $customerClassification);

        if ($customerClassification->save())
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
    }

    private function validateRequest($request) {
        $request->validate([
            'group_code' => 'required|max:25',
            'description' => 'required|max:250',
        ]);
    }

    private function assignValues($request, $customerClassification) {
        $customerClassification->group_code = $request->group_code;
        $customerClassification->description = $request->description;
        $customerClassification->description_kh = $request->description_kh;
        $customerClassification->description_zh = $request->description_zh;
    }

    private function generateCustClassification($groupCode) {
        return collect(DB::select("select ins_generate_cust_classification('" . $groupCode . "')"))->first()->ins_generate_cust_classification;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerManagement\CustomerClassification  $customerClassification
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerClassification $customerClassification)
    {
        return $customerClassification;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerManagement\CustomerClassification  $customerClassification
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerClassification $customerClassification)
    {
        return $customerClassification;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerManagement\CustomerClassification  $customerClassification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerClassification $customerClassification)
    {
        $this->validateRequest($request);

        $this->assignValues($request, $customerClassification);

        if ($customerClassification->save())
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerManagement\CustomerClassification  $customerClassification
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerClassification $customerClassification)
    {
        $customerClassification->status = 'DEL';

        if ($customerClassification->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
