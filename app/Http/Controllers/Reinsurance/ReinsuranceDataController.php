<?php

namespace App\Http\Controllers\Reinsurance;

use App\Http\Controllers\Controller;
use App\Models\Insurance\AutoDetail;
use App\Models\Insurance\BasePolicy;
use App\Models\Insurance\ReinsuranceData;
use Illuminate\Http\Request;

class ReinsuranceDataController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stores(Request $request)
    {
        $this->validateData($request);

        $reinsuranceData = collect($request->reinsurances);

        $newShareCountPercentage = $reinsuranceData->reduce(function($carry, $item) {
            return $carry + optional($item)['share'];
        });

        $totalExistingShares = ReinsuranceData::getTotalShares($request->policy_id, $request->detail_id);
        $totalExistingSharePercentage = $totalExistingShares * 100;

        $existingReinsurance = collect($request->{$request->detail_id});

        $totalOnScreenExistingSharePercentage = $existingReinsurance->sum('share');

        $checkIfOverLimit = $this->checkIfOverLimit($totalExistingSharePercentage, $totalOnScreenExistingSharePercentage, $newShareCountPercentage);

        if ($checkIfOverLimit->isOverLimit)
            return response([
                "message" => "Share is over limit, remaining is " . $checkIfOverLimit->remaining . '%'
            ], 400);

        $this->updates($existingReinsurance);
        $this->deleteReinsurance($request->deletedReinsuranceIdList);

        if (!$request->detail_id)
            return response([
                'success' => true,
                'message' => "Reinsurances updated successfully!"
            ], 201);

        $auto = AutoDetail::find($request->detail_id)->auto()->select('id', 'product_code')->first();

        $reinsurances = $reinsuranceData->map(function($item) use ($request, $auto) {
            $item = (object) $item;
            if($request->isEndorsement){
                $document_no = BasePolicy::where('id',$request->policy_id)->value('document_no');
                return [
                    'data_id' => $auto->id,
                    'detail_id' => $request->detail_id,
                    'product_line_code' => $request->product_line_code,
                    'product_code' => $auto->product_code,
                    // 'uw_year' => optional($item)->uw_year,
                    'treaty_code' => optional($item)->treaty_code,
                    'lvl' => 1,
                    'share' => optional($item)->share,
                    'ri_commission' => optional($item)->ri_commission,
                    'tax_fee' => optional($item)->tax_fee,
                    'endorsement_state' => 'ADDITION',
                    'endorsement_stage' => $document_no
                ];
            }
            // else it is a policy
            return [
                'data_id' => $auto->id,
                'detail_id' => $request->detail_id,
                'product_line_code' => $request->product_line_code,
                'product_code' => $auto->product_code,
                // 'uw_year' => optional($item)->uw_year,
                'treaty_code' => optional($item)->treaty_code,
                'lvl' => 1,
                'share' => optional($item)->share,
                'ri_commission' => optional($item)->ri_commission,
                'tax_fee' => optional($item)->tax_fee,
                'endorsement_state' => 'POLICY'
            ];
        });

        $policy = BasePolicy::find($request->policy_id);

        $policy->reinsuranceData()->createMany($reinsurances);

        return response([
            'success' => true,
            'message' => "Reinsurances added successfully!"
        ], 201);
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
                'reinsurances.*.treaty_code' => 'required|max:50',
                'reinsurances.*.share' => 'required|numeric|gte:0',
                'reinsurances.*.ri_commission' => 'required|numeric|gte:0',
                'reinsurances.*.tax_fee' => 'required|numeric|gte:0',
                // 'reinsurances.*.uw_year' => 'required|size:4',
            ],
            [
                'reinsurances.*.treaty_code.required' => 'Participant is required.',
                'reinsurances.*.treaty_code.max' => 'Participant may not be greater than :max characters.',
                'reinsurances.*.share.required' => 'Share is required.',
                'reinsurances.*.share.gte' => 'Share must be greater than or equal 0.',
                'reinsurances.*.ri_commission.required' => 'RI Commission is required.',
                'reinsurances.*.ri_commission.gte' => 'RI Commission must be greater than or equal 0.',
                'reinsurances.*.tax_fee.required' => 'Tax Fee is required.',
                'reinsurances.*.tax_fee.gte' => 'Tax Fee must be greater than or equal 0.',
                // 'reinsurances.*.uw_year.required' => 'UW Year is required.',
                // 'reinsurances.*.uw_year.size' => 'UW Year must be 4 digits.',
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

    public function getSum($policyId, $detailId, $endorsementNo = null) {
        if($endorsementNo){
            $hasAddedVehicle = ReinsuranceData::where('policy_id', $policyId)
                                                ->where('detail_id', $detailId)
                                                ->where('endorsement_state','ADDITION')
                                                ->first();
            $hasDeletedVehicle = ReinsuranceData::where('policy_id', $policyId)
                                                ->where('detail_id', $detailId)
                                                ->where('endorsement_state','DELETION')
                                                ->first();
            $isCancelledEndorsement = ReinsuranceData::where('policy_id', $policyId)
                                                ->where('detail_id', $detailId)
                                                ->where('endorsement_state','CANCEL')
                                                ->first();
            if($hasAddedVehicle)
                return (new ReinsuranceData())->getSumAddedVehicleByPolicyId($policyId, $detailId);
            if($hasDeletedVehicle)
                return (new ReinsuranceData())->getSumDeletedVehicleByPolicyId($policyId, $detailId);
            if($isCancelledEndorsement)
                return (new ReinsuranceData())->getSumCancelledVehicleByPolicyId($policyId, $detailId, $endorsementNo);
        }

        return (new ReinsuranceData())->getSumByPolicyId($policyId, $detailId);
    }
}
