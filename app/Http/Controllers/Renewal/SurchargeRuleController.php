<?php

namespace App\Http\Controllers\Renewal;

use App\Http\Controllers\Controller;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use App\Models\Renewal\SurchargeRule;

class SurchargeRuleController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->middleware('has-permission:SURCHARGE_RULE.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:SURCHARGE_RULE.NEW')->only('store');
        $this->middleware('has-permission:SURCHARGE_RULE.UPDATE')->only('update');
        $this->middleware('has-permission:SURCHARGE_RULE.DELETE')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            SurchargeRule::select('id', 'claim_ratio_from', 'claim_ratio_to', 'surcharge', 'remark')
                ->where('status', 'ACT')
                ->orderBy('id')
        );
    }

    public function show(SurchargeRule $surcharge_rule)
    {
        return $surcharge_rule;
    }

    private function assignValues($request, $surcharge_rule)
    {
        $surcharge_rule->claim_ratio_from = $request->post('claim_ratio_from');
        $surcharge_rule->claim_ratio_to = $request->post('claim_ratio_to');
        $surcharge_rule->surcharge = $request->post('surcharge');
        $surcharge_rule->remark = $request->post('remark');
    }

    public function store(Request $request)
    {
        $this->validateRequest($request, null);

        $surcharge_rule = new SurchargeRule();
        $this->assignValues($request, $surcharge_rule);

        if ($surcharge_rule->save()) {
            return [
                'success' => true,
                'message' => 'Surcharge Rule is successfully created.'
            ];
        }
    }

    private function validateRequest($request, $surcharge_rule)
    {
        $request->validate([
            'claim_ratio_from' =>'required|gt:0|numeric',
            'claim_ratio_from' =>'nullable|gt:0|numeric',
            'surcharge' => 'required|gt:0|numeric',
        ]);
    }

    public function update(Request $request, SurchargeRule $surcharge_rule)
    {
        $this->validateRequest($request, $surcharge_rule);
        $this->assignValues($request, $surcharge_rule);

        if ($surcharge_rule->save()) {
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
    public function destroy(SurchargeRule $surcharge_rule)
    {
        $surcharge_rule->status = "DEL";

        if ($surcharge_rule->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
