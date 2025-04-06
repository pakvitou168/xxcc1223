<?php

namespace App\Http\Controllers\Travel\Policy;

use App\Http\Controllers\Controller;
use App\Models\Travel\Policy\DataMaster;
use App\Models\Travel\Policy\Policy;
use App\Services\Travel\Policy\PolicyService;
use App\Traits\DataTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PolicyController extends Controller
{
    use DataTable;
    CONST ERROR_MESSAGE = "Something went wrong!";
    public function __construct(private PolicyService $policyService)
    {
        $this->middleware('has-permission:TV_POLICY.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:TV_POLICY.DELETE')->only(['destroy']);
    }

    /**
     * Display a listing of policies.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $trace_id = Str::uuid()->toString();

        try {
            return $this->generateTableData($this->policyService->list());
        } catch (\Exception $e) {
            DB::rollBack();

            info($e->getMessage(), [
                'trace_id' => $trace_id,
                'message' => self::ERROR_MESSAGE,
            ]);

            return response()->json([
                'message' =>  Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
                'trace_id' => $trace_id
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified policy.
     *
     * @param  mixed  $policy
     * @return JsonResponse
     */
    public function show($policy): JsonResponse
    {
        return response()->json($this->policyService->detail($policy));
    }

    /**
     * Remove the specified policy from storage.
     *
     * @param  mixed  $policy
     * @return JsonResponse
     */
    public function destroy($policy): JsonResponse
    {
        $trace_id = Str::uuid()->toString();

        try {
            DB::beginTransaction();
            $this->policyService->delete($policy);
            DB::commit();

            return response()->json(['message' => 'Policy deleted successfully.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();

            if($e->getCode() === Response::HTTP_NOT_FOUND) {
                return response()->json([
                    'message' => self::ERROR_MESSAGE,
                    'trace_id' => $trace_id
                ], Response::HTTP_NOT_FOUND);
            }

            info($e->getMessage(), [
                'trace_id' => $trace_id,
                'data_id' => $policy
            ]);

            return response()->json([
                'message' =>  Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
                'trace_id' => $trace_id
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function update(Request $request, $policy)
    {
        $trace_id = Str::uuid()->toString();

        try {

            $request->validate([
                'business_type' => 'required',
                'policy_type' => 'required',
            ]);

//        $this->validateRequest($request);


            $policy = Policy::where('id', $policy)->first();

            $this->assignValues($request, $policy);
            $policy->save();
            return response()->json([
                'message' => 'Policy updated successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();

            if($e->getCode() === Response::HTTP_NOT_FOUND) {
                return response()->json([
                    'message' => self::ERROR_MESSAGE,
                    'trace_id' => $trace_id
                ], Response::HTTP_NOT_FOUND);
            }

            info($e->getMessage(), [
                'trace_id' => $trace_id,
                'data_id' => $policy
            ]);

            return response()->json([
                'message' => self::ERROR_MESSAGE,
                'trace_id' => $trace_id
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateRequest($request)
    {
        $request->validate([
            'business_type' => 'required',
            'policy_type' => 'required',
        ]);
    }

    private function assignValues($request, $model)
    {
        $model->business_type = $request->post('business_type');
        $model->policy_type = $request->post('policy_type');
    }

    public function updateIssuedDate( $id)
    {
        $model = DataMaster::where('id', $id)->first();
        // Manually update updated_at for showing issue date
        $model->updated_at = now();

        if ($model->save()) {
            return response()->json([
                'message' => 'Issue Date updated successfully.'
            ], Response::HTTP_OK);
        }else {
            return response()->json([
                'message' =>  Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function exportInsuredPerson($id)
    {
    }


}