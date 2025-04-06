<?php

namespace App\Http\Controllers\Reinsurance;

use App\Http\Controllers\Controller;
use App\Models\HS\DataMaster;
use App\Models\HS\Insurance\ReinsuranceData;
use App\Models\HS\Policy;
use App\Models\Product\Product;
use Illuminate\Http\Request;

class HSReinsuranceDataController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateData($request);

        $reinsuranceData = collect($request->reinsurances);

        $newShareCountPercentage = $reinsuranceData->reduce(function($carry, $item) {
            return $carry + optional($item)['share'];
        });

        $totalExistingShares = ReinsuranceData::getTotalShares($request->policy_id);
        $totalExistingSharePercentage = $totalExistingShares * 100;

        $existingReinsurance = collect($request->existedReinsurances);

        $totalOnScreenExistingSharePercentage = $existingReinsurance->sum('share');

        $checkIfOverLimit = $this->checkIfOverLimit($totalExistingSharePercentage, $totalOnScreenExistingSharePercentage, $newShareCountPercentage);

        if ($checkIfOverLimit->isOverLimit)
            return response([
                "message" => "Share is over limit, remaining is " . $checkIfOverLimit->remaining . '%'
            ], 400);

        $this->updates($existingReinsurance);
        $this->deleteReinsurance($request->deletedReinsuranceIdList);

        if (empty($request->reinsurances)) dd($request->reinsurances);
            return response([
                'success' => true,
                'message' => "Reinsurances updated successfully!"
            ], 201);

        $hs = DataMaster::find($request->data_id)->select('id', 'product_code')->first();
        $productLineCode=Product::where('code', $hs->product_code)->value('product_line_code');
     
        $reinsurances = $reinsuranceData->map(function($item) use ($productLineCode, $hs) {
            $item = (object) $item;
                return [
                'data_id' => $hs->id,
                'product_line_code' => $productLineCode,
                'product_code' => $hs->product_code,
                'treaty_code' => optional($item)->treaty_code,
                'lvl' => 1,
                'share' => optional($item)->share,
                'ri_commission' => optional($item)->ri_commission,
                'tax_fee' => optional($item)->tax_fee,
                'endorsement_state' => 'POLICY'
            ];
        });

        $policy = Policy::find($request->policy_id);

        $policy->reinsuranceData()->createMany($reinsurances);

        return response([
            'success' => true,
            'message' => "Reinsurances added successfully!"
        ], 200);
    }

    private function updates($data) {
        $data->each(function($item) {
            ReinsuranceData::find($item['id'])->update(
                [
                    'share' => $item['share'],
                    'tax_fee' => $item['tax_fee'],
                    'ri_commission' => $item['ri_commission'],
                ]
            );
        });
    }

    private function deleteReinsurance($reinsuranceIdList){
        ReinsuranceData::whereIn('id', $reinsuranceIdList)->update(['status' => 'DEL']);
    }

    private function validateData($request) {
        $request->validate(
            [
                'reinsurances.*.partner_group' => 'required|max:50',
                'reinsurances.*.treaty_code' => 'required|max:50',
                'reinsurances.*.share' => 'required|numeric|gte:0',
                'reinsurances.*.ri_commission' => 'required|numeric|gte:0',
                'reinsurances.*.tax_fee' => 'required|numeric|gte:0',
            ],
            [
                'reinsurances.*.partner_group.required' => 'Partner Group is required.',
                'reinsurances.*.treaty_code.required' => 'Participant is required.',
                'reinsurances.*.treaty_code.max' => 'Participant may not be greater than :max characters.',
                'reinsurances.*.share.required' => 'Share is required.',
                'reinsurances.*.share.gte' => 'Share must be greater than or equal 0.',
                'reinsurances.*.ri_commission.required' => 'RI Commission is required.',
                'reinsurances.*.ri_commission.gte' => 'RI Commission must be greater than or equal 0.',
                'reinsurances.*.tax_fee.required' => 'Tax Fee is required.',
                'reinsurances.*.tax_fee.gte' => 'Tax Fee must be greater than or equal 0.',
            ]
        );
    }

    // Check if existing total share and share count is over 100
    private function checkIfOverLimit($totalExistingSharePercentage, $totalOnScreenExistingSharePercentage, $newShareCountPercentage) {

        if ($totalOnScreenExistingSharePercentage + $newShareCountPercentage <= 100)
            return (object) [
                'isOverLimit' => false
            ];

        $remainingPercentage = round(100 - $totalExistingSharePercentage, 2);

        return (object) [
            'isOverLimit' => true,
            'remaining' => $remainingPercentage
        ];
    }

    public function getSum($policyId) {
    
        return (new ReinsuranceData())->getSumByPolicyId($policyId);
    }
}
