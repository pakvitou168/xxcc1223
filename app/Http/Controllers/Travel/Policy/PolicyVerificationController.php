<?php

namespace App\Http\Controllers\Travel\Policy;

use App\Http\Controllers\Controller;
use App\Models\Travel\Policy\Insurance\ReinsuranceData;
use App\Models\Travel\Policy\Policy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PolicyVerificationController extends Controller
{
    CONST ERROR_MESSAGE = "Something went wrong!";
    /**
     * Check if policy reinsurance is completed.
     *
     * @param  Policy  $policy
     * @return JsonResponse
     */
    public function isPolicyReinsuranceCompleted(Policy $policy): JsonResponse
    {
        return response()->json(ReinsuranceData::isReinsuranceCompleted($policy->id));
    }

    /**
     * Check if policy configuration is completed.
     *
     * @param  Policy  $policy
     * @return mixed
     */
    public function isPolicyConfigurationCompleted(Policy $policy)
    {
        return $policy->isPolicyConfigurationCompleted();
    }

    public function updateSubmitStatus(Policy $policy, Request $request)
    {
        if ($policy->approved_status !== $request->status) {
            $policy->approved_status = $request->status;

            if ($policy->save()) {
                return [
                    'success' => true,
                    'message' => 'Policy submit status has been updated to ' . $request->post('status')
                ];
            }
        }
    }
}