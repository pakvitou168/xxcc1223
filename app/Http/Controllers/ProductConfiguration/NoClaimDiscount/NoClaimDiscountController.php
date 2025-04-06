<?php

namespace App\Http\Controllers\ProductConfiguration\NoClaimDiscount;

use App\Http\Controllers\Controller;
use App\Models\NoClaimDiscount\NoClaimDiscount;
use App\Traits\DataTable;
use Illuminate\Http\Request;

class NoClaimDiscountController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(NoClaimDiscount::class, 'no_claim_discount');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            NoClaimDiscount::select('id', 'product_code', 'ncd', 'description')
                ->with(['product' => function($query){
                    $query->select('code','name')->where('status','ACT');
                }])
                ->where('status','ACT')
                ->orderBy('product_code')
                ->orderBy('ncd')
        );
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(NoClaimDiscount $no_claim_discount)
    {
        return $no_claim_discount->load('product:code,name');
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);

        $no_claim_discount = new NoClaimDiscount();
        $this->assignValues($request, $no_claim_discount);

        if ($no_claim_discount->save()) {
            return [
                'success' => true,
                'message' => 'No Claim Discount is successfully created.'
            ];
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'product_code' => 'required',
            'ncd' => 'required',
        ]);
    }

    private function assignValues($request, $no_claim_discount) {
        $no_claim_discount->product_code = $request->post('product_code');
        $no_claim_discount->ncd = $request->post('ncd');
        $no_claim_discount->description = $request->post('description');
        $no_claim_discount->status = 'ACT';
    }

    public function edit(NoClaimDiscount $no_claim_discount)
    {
        return $no_claim_discount->load('product:code,name');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NoClaimDiscount $no_claim_discount)
    {
        $this->validateRequest($request);
        $this->assignValues($request, $no_claim_discount);

        if ($no_claim_discount->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(NoClaimDiscount $no_claim_discount)
    {
        $no_claim_discount->status = "DEL";

        if ($no_claim_discount->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
