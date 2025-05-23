<?php

namespace App\Http\Controllers\Endorsement;

use App\Exports\HS\EndorsementTemplateExport;
use App\Exports\HS\NameListExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\HS\EndorsementRequest;
use App\Imports\HS\EndorsementReadout\ImportAddDelete;
use App\Imports\HS\EndorsementReadout\InsuredPersonInfo;
use App\Models\HS\DataDetail;
use App\Models\HS\DataMaster;
use App\Models\HS\Insurance\Endorsement;
use App\Models\HS\Insurance\EndorsementView;
use App\Models\HS\Insurance\ReinsuranceData;
use App\Models\HS\Policy;
use App\Models\RefEnum;
use App\Scopes\ActiveScope;
use App\Services\HS\HSEndorsementService;
use App\Services\HS\PolicyService;
use App\Services\HS\QuotationService;
use App\Traits\DataTable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class HSEndorsementController extends Controller
{
    use DataTable;

    public function __construct(private HSEndorsementService $endorsementService, private QuotationService $quotationService, private PolicyService $hsPolicyService)
    {
        $this->middleware('has-permission:HS_ENDORSEMENT.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:HS_ENDORSEMENT.NEW')->only(['store']);
        $this->middleware('has-permission:HS_ENDORSEMENT.UPDATE')->only(['update']);
        $this->middleware('has-permission:HS_ENDORSEMENT.APPROVE')->only(['approve']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            EndorsementView::with('endorsement:id,approved_reason,approved_status')
                ->orderByDesc('id'),
            function (&$datas) {
                foreach ($datas as $data) {
                    $data->total_premium = $this->endorsementService->formatPremium($data->total_premium);
                }
            }
        );
    }

    public function generate(Request $request, $id)
    {
        $request->validate([
            'type' => 'required',
            'effective_date' => 'required',
        ], [
            'type.required' => 'Endorsement Type is required.',
            'effective_date.required' => 'Endorsement Effective Date is required.',
        ]);

        if (!$this->endorsementService->isApproved($id)) {
            return response([
                'success' => false,
                'message' => 'Policy is not yet approved'
            ], 400);
        }

        DB::beginTransaction();

        try {

            $generated = $this->endorsementService->generate(
                $id,
                $request->type,
                $request->effective_date,
                $request->description,
                auth()->id()
            );

            DB::commit();

            if (!$generated) {
                throw new Exception("Unexpected Error.", 500);
            }
            return response()->json(['message' => 'Generated endorsement successfully.'], 200);
        } catch (Exception $ex) {
            report($ex);
            DB::rollBack();

            return response()->json(['message' => 'Something went wrong.'], 500);
        }
    }

    public function listEndorsementTypes()
    {
        return $this->endorsementService->listEndorsementTypes();
    }

    public function getValidPeriod($id)
    {
        return $this->endorsementService->getEffectivePeriod($id);
    }

    public function canGenerate($id)
    {
        if (!$this->endorsementService->isApproved($id))
            return false;
        // If is policy cancellation endorsement
        if ($this->endorsementService->getEndorsementType($id) === Policy::CANCELLATION_ENDORSEMENT)
            return false;
        return $this->endorsementService->isLatestEndorsement($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(EndorsementView $endorsement): EndorsementView
    {
        return EndorsementView::withCount('insuredPersons')->with('hs:id,endorsement_type,endorsement_e_date,effective_date_from,effective_date_to', 'endorsement:id,business_type,policy_type,approved_status')->find($endorsement->id);
    }

    public function getDetail($id)
    {
        return $this->endorsementService->getDetail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EndorsementRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $endorsement = $this->endorsementService->update($id, $request);
            if ($endorsement->endorsement_type == 'GENERAL') {
                $this->hsPolicyService->generateCommissionData($id);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Renewed policy updated']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(EndorsementView $endorsement)
    {
        try {
            DB::beginTransaction();
            if ($endorsement->status === 'APV') {
                throw new Exception("Endorsement has already been approved", 409);
            }
            $params = [
                $endorsement->id,
                $endorsement->product_code,
                'POLICY',
                $endorsement->hs->id
            ];
            $result = DB::select('select * from ins_hs_prod_cancel_endorsement(?,?,?,?)', $params);

            if (optional($result[0])->code !== 'SUC') {
                Log::error("Delete endorsement failed", $result);
                throw new Exception("Endorsement deleting failed");
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Endorsement successfully deleted']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => 'Something went wrong'], $e->getCode() ?? 500);
        }
    }

    public function showDetail(EndorsementView $endorsement)
    {

        $dataMaster = DataMaster::find($endorsement->data_id);
        $data['endorsement_type'] = optional(RefEnum::listEndorsementTypes('HS_ENDORSEMENT_TYPE'))[preg_replace('/\s+/', '', $dataMaster->endorsement_type)];
        $data['endorsement_description'] = nl2br($endorsement->endorsement_description);
        $data['product'] = $endorsement->product;
        $data['endorsement_premium'] = $endorsement->total_premium;
        return ['hs' => $this->endorsementService->getDataDetail($endorsement->data_id), ...$data];
    }

    public function approve(Request $request, Endorsement $endorsement)
    {
        try {
            DB::beginTransaction();
            $isReinsuranceCompleted = ReinsuranceData::isReinsuranceCompleted($endorsement->id);
            $isConfigurationCompleted = $endorsement->isPolicyConfigurationCompleted();
            if (!$isReinsuranceCompleted || !$isConfigurationCompleted) {
                throw new Exception("Endorsement data has not completed!", 400);
            }

            if ($this->checkMakerAndApprover($endorsement)) {
                throw new Exception("You can not approve your own Endorsement.", 403);
            }
            if ($endorsement->status == $request->approved_status) {
                throw new Exception("Endorsement has already been updated", 409);
            }

            $endorsement->status = $request->approved_status;
            $endorsement->approved_reason = $request->approved_reason;
            $endorsement->approved_at = now();
            $endorsement->approved_by = auth()->id();
            $endorsement->save();

            if ($request->approved_status == 'APV' && $endorsement->hs->endorsement_type !== 'GENERAL') {
                $endorsementView = EndorsementView::find($endorsement->id);
                $endorsementPremium = $this->getEndorsementPremium($endorsementView);
                $requestType = $endorsementPremium > 0 ? 'INVOICE' : ($endorsementPremium < 0 ? 'CREDIT_NOTE' : null);
                if (!is_null($requestType)) {
                    info("Credit note gen.", ['document no' => $endorsementView->document_no, 'requestType' => $requestType, 'user_id' => auth()->id(), 'premium' => $endorsementPremium]);
                    $this->generateInvoice($endorsementView->document_no, $requestType);
                }else{
                    info("error",['premium' => $endorsementPremium,'request type' => $requestType,'data_id' => $endorsement->data_id]);
                    throw new Exception("Sth went wrong");
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $request->approved_status == 'APV' ? 'Approved successfully' : 'Rejected successfully'
            ]);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    private function generateInvoice($documentNo, $requestType)
    {
        $params = [
            $documentNo,
            $requestType,
            auth()->id(),
        ];
        $hsInvoice = DB::select("select * from ins_generate_policy_invoice_note(?,?,?)", $params);
        info('Invoice Generation', $hsInvoice);
        if (empty($hsInvoice))
            throw new Exception("Generating invoice failed", 500);
    }

    private function getEndorsementPremium(EndorsementView $endorsement)
    {
        $totalPremium = 0;
        if ($endorsement->endorsement_type === 'ADD/DELETE') {
            $totalPremium = DataDetail::whereMasterDataId($endorsement->data_id)->whereNotNull('endorsement_state')->sum('premium');
        } elseif ($endorsement->endorsement_type === 'CANCELLATION') {
            $totalPremium = $endorsement->total_premium;
        } elseif (!$endorsement->endorsement_type) {
            throw new Exception("Endorsement type not found");
        }
        return floatval($totalPremium);
    }

    public function getPremium(EndorsementView $endorsement, $rawNumber = false)
    {
        $endorsementPremium = DataMaster::withoutGlobalScopes([ActiveScope::class])
            ->find($endorsement->data_id)
            ->total_premium;

        if ($rawNumber) {
            return $endorsementPremium;
        }

        if ($endorsementPremium < 0) {
            return '(' . number_format(abs($endorsementPremium), 2, '.', ',') . ')';
        }

        return number_format($endorsementPremium, 2, '.', ',');
    }

    private function checkMakerAndApprover(Endorsement $endorsement)
    {
        return $endorsement->updated_by ? $endorsement->updated_by == auth()->id() : $endorsement->created_by == auth()->id();
    }

    public function configure(Request $request, Policy $policy)
    {
        try {
            DB::beginTransaction();
            $this->endorsementService->updateConfig($policy, $request);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Endorsement configuration successfully updated']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    public function exportTemplate(Policy $policy)
    {
        if ($policy->dataMaster->endorsement_type === 'GENERAL') { // in case general endorsement => export insured only
            return Excel::download(new NameListExport($policy), 'insured-persons.xlsx');
        }
        return Excel::download(new EndorsementTemplateExport($policy), 'endorsement.xlsx');
    }

    public function importEndorsement(Request $request, Policy $policy)
    {
        try {
            // DB::beginTransaction();
            if ($policy->status !== 'PND' || (!in_array($policy->endorsement_type, ['ADD/DELETE', 'GENERAL']) && $policy->approved_status !== 'PRG')) {
                throw new Exception("Endorsement has already been updated", 409);
            } else {
                if ($policy->dataMaster->endorsement_type === 'ADD/DELETE') {
                    $data = Excel::toArray(new ImportAddDelete, $request->file('file'));
                    $this->endorsementService->manipulateEndorsement($policy, $data);
                } elseif ($policy->dataMaster->endorsement_type === 'GENERAL') {
                    $data = Excel::toArray(new InsuredPersonInfo, $request->file('file'));
                    $this->endorsementService->manipulateInsuredInfoEndorsement($policy, $data);
                } else {
                    throw new Exception("Endorsement type not found", 404);
                }
            }

            // DB::commit();
            return response()->json(['success' => true, 'message' => 'Endorsement data has been imported']);
        } catch (Exception $e) {
            // DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => "Something went wrong", 'error' => $e->getMessage()], 500);
        }
    }

    public function saveCancelPolicyEndorsement(Request $request, Endorsement $endorsement)
    {
        try {
            DB::beginTransaction();
            $endorsement->endorsement_description = $request->endorsement_description;
            $endorsement->save();
            $response = $this->cancelPolicyEndorsement($endorsement);
            $dataMaster = DataMaster::find($endorsement->data_id);
            $dataMaster->endorsement_e_date = $request->endorsement_e_date;
            $dataMaster->refund_option = $request->refund_option;

            // Only refund type custom has custom refund amount
            if ($request->refund_option === 'CUSTOM' || $request->refund_option === 'SPECIAL_REFUND')
                $dataMaster->custom_refund_amount = optional($request)->custom_refund_amount * (-1);
            else
                $dataMaster->custom_refund_amount = null;
            $dataMaster->save();
            $response = $this->cancelPolicyEndorsement($endorsement);
            DB::commit();
            if (optional($response[0])->code === 'SUC')
                return [
                    'success' => true,
                    'message' => 'Policy cancelled successfully.'
                ];

        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong'], $e->getCode() > 1 ? $e->getCode() : 500);
        }
    }

    private function cancelPolicyEndorsement($endorsement)
    {
        $params = [
            $endorsement->id,
            optional($endorsement->hs)->product_code,
            'POLICY',
            $endorsement->data_id,
            auth()->id()
        ];
        return DB::select("select * from ins_hs_do_endor_policy_cancellation(?,?,?,?,?)", $params);
    }

    public function coverDetail(EndorsementView $endorsement)
    {
        $hs = $endorsement->hs;
        $hs->address = $hs->customer->correspondenceAddress();
        $hs->period_of_insurance = $hs->insuredPeriod();
        $hs->issued_by = $hs->issuedByName($hs->updated_by ?? $hs->created_by);
        return ['hs' => $hs];
    }

    public function updateDesc(Request $request, Endorsement $endorsement)
    {
        try {
            DB::beginTransaction();
            $endorsement->endorsement_description = $request->endorsement_description;
            $endorsement->update();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Update success']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }
}
