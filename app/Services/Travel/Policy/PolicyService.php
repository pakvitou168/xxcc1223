<?php

namespace App\Services\Travel\Policy;

use App\Models\RecordStatus;
use App\Models\Travel\Policy\DataMaster;
use App\Models\Travel\Policy\Insurance\PolicyCommissionData;
use App\Models\Travel\Policy\PolicyV;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class PolicyService
{
    /**
     * @param DataMaster $master
     * @param PolicyV $policyV
     * @param PolicyFormatterService $formatter
     * @param PolicyRelationshipService $relationshipService
     */
    public function __construct(
        private DataMaster $master,
        private PolicyV $policyV,
        private PolicyFormatterService $formatter,
        private PolicyRelationshipService $relationshipService
    ) {
    }

    /**
     * Get policy list query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function list()
    {
        return $this->policyV->query();
    }

    /**
     * Delete a policy by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->master->where('id', $id)->update([
            'status' => RecordStatus::DELETED
        ]);
    }

    /**
     * Get policy detail by ID
     *
     * @param int $id
     * @param string $lang
     * @return DataMaster
     * @throws ModelNotFoundException
     */
    public function detail($id, $lang = 'en'): DataMaster
    {
        // Get the relationships needed for policy detail
        $relationships = $this->relationshipService->getDetailRelationships($lang);

        // Build and execute the query
        $query = $this->master->with($relationships);
        
        $data = $query->find($id);

        if (!$data) {
            throw new ModelNotFoundException("Policy not found", 404);
        }

        // Format and enrich the policy data through the formatter service
        return $this->formatter->formatPolicyDetail($data, $lang);
    }

    public function generateCommissionData($policyId, $request)
    {
        $model = new PolicyCommissionData();
        $model->policy_id = $policyId;
        $model->policy_no = $request->policy_no;
        $model->business_category = $request->business_category;
        $model->business_code = $request->business_code;
        $model->gross_written_premium = $request->gross_written_premium;
        $model->premium_tax_fee_rate = $request->premium_tax_fee_rate;
        $model->premium_tax_fee = $request->premium_tax_fee;
        $model->net_written_premium = $request->net_written_premium;
        $model->commission_rate = $request->commission_rate;
        $model->commission_amount = $request->commission_amount;
        $model->witholding_tax_rate = $request->witholding_tax_rate;
        $model->witholding_tax = $request->witholding_tax;
        $model->commission_due_amount = $request->commission_due_amount;

        /*ins_tv_policy_commission_data*/
        if ($model->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Generated successfully!',
            ]);
        }
    }
}