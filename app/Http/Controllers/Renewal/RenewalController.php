<?php

namespace App\Http\Controllers\Renewal;

use App\Exports\RenewalExport;
use App\Exports\VehiclesRenewalExport;
use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Renewal\Renewal;
use App\Models\Renewal\RenewalListExportView;
use App\Models\Renewal\RenewalListV;
use App\Models\Renewal\RenewalNoticeCoverV;
use App\Models\Renewal\RenewalNoticeDeductibleV;
use App\Models\Renewal\RenewalNoticeMainV;
use App\Models\Renewal\RenewalNoticeVehicleV;
use App\Models\UserManagement\User\UserFile;
use App\Services\Renewal\RenewalService;
use App\Traits\DataTable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\App;

class RenewalController extends Controller
{
    use DataTable;

    public function __construct(private RenewalService $renewalService)
    {
        $this->middleware('has-permission:RENEWAL.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:RENEWAL.APPROVE')->only('approve');
        $this->middleware('has-permission:RENEWAL.REVISE')->only('revise');
        $this->middleware('has-permission:RENEWAL.ACCEPT')->only('accept');
        $this->middleware('has-permission:RENEWAL.UPDATE')->only(['edit', 'submit']);
        $this->middleware('has-permission:RENEWAL.LOAD')->only('generateRenewalList');
        $this->middleware('has-permission:RENEWAL.PROCESS')->only(['generateRenewedPolicy', 'autoApproveNoClaimPolicies']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            RenewalListV::latest('id')
        );
    }

    public function generateRenewalList(Request $request)
    {

        $request->validate([
            'uw_year' => 'required',
            'expired_date_from' => 'required|date',
            'expired_date_to' => 'required|date',
        ], [
            'uw_year.required' => 'Underwriting Year is required',
            'expired_date_from.required' => 'From Date is required.',
            'expired_date_from.date' => 'From Date must be a date.',
            'expired_date_to.required' => 'To Date is required.',
            'expired_date_to.date' => 'To Date must be a date.',
        ]);

        try {
            $generated = $this->renewalService->generateRenewalList($request->uw_year, $request->expired_date_from, $request->expired_date_to);

            if ($generated) {
                if ($this->generateAllNoDetailRenewals()) {
                    return response([
                        'success' => true,
                        'message' => 'Generated Renewal List.'
                    ]);
                }
            }
        } catch (Exception $e) {
            report($e);
            abort(500, 'Server Error');
        }
    }

    private function generateAllNoDetailRenewals()
    {
        $noDetailRenewalIds = $this->renewalService->listNoDetailRenewals()->pluck('id');
        DB::beginTransaction();

        try {
            collect($noDetailRenewalIds)->each(function ($item) {
                $generated = $this->renewalService->generateRenewalDetail($item);

                if (!$generated)
                    throw new Exception('Some Records are error');
            });

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            abort(500, 'Server Error');
        }
    }

    public function autoApproveNoClaimPolicies()
    {
        try {
            $generated = $this->renewalService->autoApproveNoClaimPolicies();

            if ($generated) {
                return response([
                    'success' => true,
                    'message' => 'Approved successfully'
                ]);
            }
            return response([
                'success' => false,
                'message' => 'Approved failed'
            ], 400);
        } catch (Exception $e) {
            report($e);
            abort(500, 'Server Error');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return boolean
     */
    private function hasPassengerOrTonnage($productCode)
    {
        $specification = Product::getProductSpecificationByCode($productCode);

        return $specification === 'TONNAGE' || $specification === 'PASSENGER';
    }

    public function show($id)
    {
        try {
            $data = RenewalNoticeMainV::where('renew_policy_id', $id)->firstOr(fn() => throw new Exception("Policy with id $id not found"));
            $data->exclusion_clauses = explode(';', $data->exclusion_clause);
            $data->endorsement_clauses = explode(";", $data->endorsement_clause);
            $data->has_passenger_tonnage = $this->hasPassengerOrTonnage($data->product_code);
            $data->signature = $this->getSignatureByRenewal($data);
            if ($data->updated_by)
                $data->issued_by = $data->issuedByName($data->updated_by);
            else if ($data->created_by)
                $data->issued_by = $data->issuedByName($data->created_by);
            else
                $data->issued_by = null;
            $data['vehicles'] = RenewalNoticeVehicleV::where('renew_policy_id', $id)->get();
            $data->total_sum_insured = $data['vehicles']->sum('sum_insured');
            $data->total_premium = $data['vehicles']->sum('premium');
            $data['deductibles'] = RenewalNoticeDeductibleV::where('renew_policy_id', $id)->get();
            $data['cover'] = RenewalNoticeCoverV::where('renew_policy_id', $id)->get()
                ->map(function ($item) {
                    $item->html_detail = nl2br($item->cover_detail);
                    return $item;
                });

            return response([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function getSignatureByRenewal($renewal)
    {
        return UserFile::select('file_url')->where('user_id', $renewal->approved_by)->where('file_type', 'SIGNATURE')->first();
    }

    public function exportVehicles($id, $document_no)
    {
        return Excel::download(new VehiclesRenewalExport($id, $document_no), $document_no . '.xlsx');
    }

    public function downloadRenewal(Request $request, $id, $lang)
    {
        App::setLocale($lang);
        $data = $this->renewalDetail($id, $lang);
        if ($data->submit_status == 'APV') {
            $data->signature = UserFile::select('file_url')->where('user_id', $data->approved_by)->where('file_type', 'SIGNATURE')->first();
            if ($data->signature)
                if (!$data->signature->file_url)
                    $data->signature = null;
        }
        $data['hasLetterHead'] = $request->get('letterhead');
        $documentNo = $data->document_no;
        $data['documentNo'] = $documentNo;

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('title', 'PGI');
        $pdf->setOption('margin-top', 32);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);

        $pdf->setOption('footer-right', 'Page: [page] of [topage]          ');
        $pdf->setOption('footer-font-size', 8);
        $pdf->setOptions([
            'header-html' => view('pdf.header', [
                'documentNo' => $documentNo,
                'hasLetterHead' => $request->get('letterhead')
            ]),
            'footer-html' => view('pdf.footer', [
                'hasLetterHead' => $request->get('letterhead')
            ]),
        ]);

        $pdf->loadView('pdf.renewals.renewal_notice', compact('data'));

        return $pdf->stream($documentNo . '.pdf');
    }

    private function renewalDetail($id, $lang)
    {
        $data = RenewalNoticeMainV::where('renew_policy_id', $id)->firstOrFail();
        $data->exclusion_clauses = explode(";", $lang == 'en' ? $data->exclusion_clause : $data->exclusion_clause_kh);
        $data->endorsement_clauses = explode(";", $lang == 'en' ? $data->endorsement_clause : $data->endorsement_clause_kh);
        if ($data->updated_by)
            $data->issued_by = $data->issuedByName($data->updated_by);
        else if ($data->created_by)
            $data->issued_by = $data->issuedByName($data->created_by);
        else
            $data->issued_by = null;

        $data['vehicles'] = RenewalNoticeVehicleV::where('renew_policy_id', $id)->get();
        $data->total_sum_insured = $data['vehicles']->sum('sum_insured');
        $data->total_premium = $data['vehicles']->sum('premium');
        $data['deductibles'] = RenewalNoticeDeductibleV::where('renew_policy_id', $id)->get();
        $data['coverage'] = RenewalNoticeCoverV::where('renew_policy_id', $id)->get()
            ->map(function ($item) {
                $item->html_detail = nl2br($item->cover_detail);
                $item->html_detail_kh = nl2br($item->cover_detail_kh);
                return $item;
            });
        $nf_discount = str_replace('/\s+/', '', $data->discount);
        $nf_discount = preg_replace("/,|%/", '', $nf_discount);
        $data['nf_discount'] = floatval($nf_discount);
        return $data;
    }

    public function generateRenewedPolicy($id)
    {
        $canGenerate = $this->renewalService->canGenerateRenewedPolicy($id);

        if (!$canGenerate)
            throw new \Exception("Unable to proceed");

        try {
            $generated = $this->renewalService->generateRenewedPolicy($id);

            if ($generated) {
                return response([
                    'success' => true,
                    'message' => 'Proceed to Policy successfully'
                ]);
            }
            throw new \Exception("Unexpected Error", 400);
        } catch (\Exception $e) {
            report($e);
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function canGenerateRenewedPolicy($id)
    {
        $canGenerate = $this->renewalService->canGenerateRenewedPolicy($id);
        if ($canGenerate) {
            return [
                'success' => true,
            ];
        } else {
            return response([
                'success' => false,
            ], 400);
        }
    }

    public function approve(Request $request, $id)
    {
        if (auth()->id() == $this->getMakerId($id)) {
            abort(403, "Maker can not approve their own records.");
        }

        $canBeApproved = $this->renewalService->canBeApproved($id);
        if (!$canBeApproved)
            throw new \Exception("Unable to proceed");

        $data = [
            'submit_status' => $request->status,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approved_reason' => $request->reason,
        ];

        $updated = $this->renewalService->update($data, $id);

        if ($updated) {
            return response([
                'success' => true,
                'message' => 'Approved successfully'
            ]);
        } else {
            throw new \Exception("Unexpected Error", 400);
        }
    }

    public function accept(Request $request, $id)
    {
        if (auth()->id() == $this->getMakerId($id)) {
            abort(403, "Maker can not approve their own records.");
        }

        $canBeAccepted = $this->renewalService->canBeAccepted($id);
        if (!$canBeAccepted)
            throw new \Exception("Unable to proceed");

        $data = [
            'accept_status' => $request->status,
            ...($request->status === Renewal::REJECTED ? ['status' => Renewal::LOSS] : []),
            'accepted_by' => auth()->id(),
            'accepted_at' => now(),
            'accepted_reason' => $request->reason,
        ];

        $updated = $this->renewalService->update($data, $id);

        if ($updated) {
            return response([
                'success' => true,
                'message' => 'Accepted successfully'
            ]);
        } else {
            throw new \Exception("Unexpected Error", 400);
        }
    }

    public function revise($id)
    {
        $canBeRevise = $this->renewalService->canBeRevised($id);

        if (!$canBeRevise)
            throw new \Exception("Cannot be revised");

        $data = [
            'submit_status' => Renewal::PENDING,
            'approved_by' => null,
            'approved_at' => null,
            'approved_reason' => null,
        ];

        $updated = $this->renewalService->update($data, $id);

        if ($updated) {
            return response([
                'success' => true,
                'message' => 'Revised successfully'
            ]);
        } else {
            throw new \Exception("Unexpected Error", 400);
        }
    }

    public function listStatusLovs()
    {
        $statuses = [
            'PND' => 'Pending',
            'REW' => 'Renewed',
            'LOS' => 'Loss',
            'DIS' => 'Disabled',
        ];

        $submitStatuses = [
            'DRF' => 'Draft',
            'PND' => 'Pending',
            'APV' => 'Approved',
            'REJ' => 'Rejected',
        ];

        return [
            'statuses' => array_map(function($key ,$value) {
                return ['label' => $value,'value' => $key ];
            },array_keys($statuses),$statuses),
            'submit_statuses' => array_map(function($key ,$value){
                return ['label' => $value,'value' => $key ];
            },array_keys($submitStatuses),$submitStatuses)
        ];
    }

    public function generateNewVersion($id)
    {
        try {
            $canGenerate = $this->renewalService->canGenerateRenewedPolicyNewVersion($id);
            if (!$canGenerate)
                throw new \Exception("Unable to generate new version");
            $generated = $this->renewalService->generateNewReNewVersion($id);

            if ($generated) {
                return response([
                    'success' => true,
                    'message' => 'Generated new version successfully'
                ]);
            }
        } catch (\Exception $e) {
            report($e);
            abort(500, $e->getMessage());
        }
    }

    public function edit($id)
    {
        return Renewal::findOr($id, fn() => abort(404, 'Not found.'));
    }

    public function submit($id)
    {
        $data = [
            'submit_status' => Renewal::PENDING,
            'updated_at' => now(),
        ];

        if ($this->renewalService->update($data, $id)) {
            return response([
                'success' => true,
                'message' => 'Submitted successfully'
            ]);
        } else {
            throw new \Exception("Unexpected Error", 400);
        }
    }

    private function getMakerId($renewalId)
    {
        $renewal = Renewal::find($renewalId);

        return $renewal->updated_by ?: $renewal->created_by;
    }

    public function export(Request $request)
    {
        $query = RenewalListExportView::query();

        $fileName = 'Renewal List';
        if ($request->expired_date_from) {
            $query = $query->where('expiry_date', '>=', $request->expired_date_from);

            $fileName .= " from $request->expired_date_from";
        }

        if ($request->expired_date_to) {
            $query = $query->where('expiry_date', '<=', $request->expired_date_to);

            $fileName .= " to $request->expired_date_to";
        }
        $query = $query->orderBy('expiry_date');
        $count = $query->count();

        return Excel::download(new RenewalExport(
            query: $query,
            count: $count,
            expiredFrom: $request->expired_date_from,
            expiredTo: $request->expired_date_to
        ), "$fileName.xlsx");
    }
}
