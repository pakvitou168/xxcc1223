<?php

namespace App\Http\Controllers\Travel\Policy;

use App\Http\Controllers\Controller;
use App\Models\Travel\Policy\Policy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;
use Throwable;
class PolicyApprovalController extends Controller
{
    CONST ERROR_MESSAGE = "Something went wrong!";
    /**
     * Check if the current user is the maker of the policy.
     *
     * @param int $policy_id
     * @return bool
     */
    private function checkMakerAndApprover($policy_id): bool
    {
        $maker = Policy::find($policy_id);
        return $maker->updated_by ? $maker->updated_by == auth()->id() : $maker->created_by == auth()->id();
    }

    /**
     * Approve or reject a policy.
     *
     * @param Request $request
     * @param Policy $policy
     * @return JsonResponse
     */
    public function approve(Request $request, Policy $policy): JsonResponse
    {
        if ($this->checkMakerAndApprover($policy->id)) {
            throw new Exception("You can not approve your own Policy.", 403);
        }

        try {
            DB::beginTransaction();

            $policy->status = $request->approved_status;
            $policy->approved_reason = $request->approved_reason;
            $policy->approved_at = now();
            $policy->approved_by = auth()->id();

            if ($policy->save()) {
                if ($request->approved_status == 'APV') {
                    $params = [
                        $policy->document_no,
                        'INVOICE',
                        auth()->id(),
                    ];
                    $travel_policyInvoice = DB::select("select * from ins_tv_generate_policy_invoice_note(?,?,?)", $params);

                    if (!isset($travel_policyInvoice[0])) {
                        Log::error('Generate policy invoice', ['invoice' => $travel_policyInvoice]);
                        throw new Exception("Generate invoice failed", 500);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $request->approved_status == 'APV' ? 'Approved successfully' : 'Rejected successfully'
            ]);
        } catch(Throwable $th) {
            DB::rollBack();

            $trace_id = Str::uuid()->toString();

            Log::error('Policy approval failed', [
                'trace_id' => $trace_id,
                'policy_id' => $policy->id,
                'req_body' => json_encode($request->all()),
                'exception' => $th->getMessage(),
            ]);

            return response()->json([
                'message' => self::ERROR_MESSAGE,
                'trace_id' => $trace_id,
                'success' => false
            ], 500);
        }
    }
}