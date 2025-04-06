<?php

namespace App\Http\Controllers\PA;

use App;
use App\Http\Controllers\Controller;
use App\Http\Requests\PA\CommissionRequest;
use App\Http\Requests\PA\EndorsementRequest;
use App\Http\Requests\PA\PolicyApproveRequest;
use App\Http\Requests\PA\PolicyConfigRequest;
use App\Http\Requests\PA\ReinsuranceRequest;
use App\Models\BankInformation\BankInformation;
use App\Models\UserManagement\User\UserFile;
use App\Services\PA\PolicyService;
use App\Traits\DataTable;
use DB;
use Exception;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    use DataTable;

    public function __construct(private PolicyService $policyService)
    {
    }

    public function index()
    {
        return $this->generateTableData($this->policyService->list());
    }

    public function show($id)
    {
        return response()->json($this->policyService->detail($id));
    }
    public function commission($id)
    {
        return response()->json($this->policyService->commission($id));
    }
    public function updateCommission(CommissionRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->policyService->updateCommission($request->validated(), $id);
            DB::commit();
            return response()->json(['message' => 'Update success']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
    public function configuration(Request $request, $id)
    {
        return response()->json($this->policyService->configuration($id));
    }
    public function updateConfig(PolicyConfigRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->policyService->updateConfig($request->validated(), $id);
            DB::commit();
            return response()->json(['message' => 'Update success']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
    public function reinsurance(Request $request, $id)
    {
        return response()->json($this->policyService->reinsurance($request, $id));
    }
    public function edit($id)
    {
        return response()->json($this->policyService->edit($id));
    }
    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->policyService->delete($id);
            DB::commit();
            return response()->json(['message' => 'Policy deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
    public function updateReinsurance(ReinsuranceRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->policyService->updateReinsurance($request->validated()['reinsurances'], $id);
            DB::commit();
            return response()->json(['message' => 'Update success']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
    public function approve(PolicyApproveRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->policyService->approve($request->validated(), $id);
            DB::commit();
            return response()->json(['message' => 'Approve success']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
    public function endorse(EndorsementRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->policyService->endorse($request->validated(), $id);
            DB::commit();
            return response()->json(['message' => 'Endorsement generated']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
    public function download($id, $lang = 'en')
    {
        $letterHead = request()->letterhead;
        $stamp = request()->get('stamp', 1);
        App::setLocale($lang);

        $data = $this->policyService->detail($id, $lang);

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('title', 'PA Policy');

        $pdf->setOption('margin-top', 36);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('footer-right', 'Page: [page] of [topage]          ');
        $pdf->setOption('footer-font-size', 8);
        $pdf->setOptions([
            'header-html' => view('pdf.header', [
                'documentNo' => $data->policy?->document_no,
                'hasLetterHead' => $letterHead
            ]),
            'footer-html' => view('pdf.footer', [
                'hasLetterHead' => $letterHead
            ]),
        ]);

        $data->hasLetterHead = $letterHead;
        if ($stamp === '0') {
            $data->signature = null;
        }

        $pdf->loadView('pdf.policies.pa.pa', [
            'data' => $data,
            'hasLetterHead' => $letterHead
        ]);

        return $pdf->stream("pa_policy-" . $data->policy?->document_no . ".pdf");
    }

    /**
     * Download invoice
     */
    public function downloadInvoice($id, $withSignature = null)
    {
        try {
            $data = $this->policyService->detail($id);
            $bank_list = BankInformation::where('status', 'ACT')->where('default', true)->get();

            $invoice = $this->policyService->getInvoiceData($id);

            $signature = null;
            if ($data->policy && $data->policy->status == 'APV' && $withSignature) {
                $signature = UserFile::select('file_url')->where('user_id', $data->policy->approved_by)
                    ->where('file_type', 'SIGNATURE')->first();
            }

            $pdf = App::make('snappy.pdf.wrapper');
            $pdf->setOption('title', 'Invoice');
            $pdf->loadView('pdf.policies.pa.invoice', [
                'invoice' => $invoice,
                'bank_list' => $bank_list,
                'signature' => $signature,
                'data' => $data
            ]);

            $pdf->setOption('page-size', 'a4');
            $pdf->setOption('disable-smart-shrinking', true);
            $pdf->setOption('margin-top', 33);
            $pdf->setOption('margin-bottom', 27);
            $pdf->setOption('margin-left', 15);
            $pdf->setOption('margin-right', 15);

            return $pdf->stream("invoice-" . ($data->policy?->document_no ?? 'no-document') . ".pdf");
        } catch (\Exception $e) {
            // Log the error
            // \Log::error('Invoice download error: ' . $e->getMessage());

            // Return an error response
            return response()->view('errors.custom', [
                'message' => 'Unable to generate invoice. Policy not found or data is incomplete.'
            ], 404);
        }
    }

    /**
     * Download certificate
     */
    public function downloadCertificate($id)
    {
        $data = $this->policyService->detail($id);

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('title', 'Certificate');

        $pdf->setOption('page-size', 'a4');
        $pdf->setOption('disable-smart-shrinking', true);
        $pdf->setOption('zoom', 1);
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-bottom', 0);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);

        $pdf->loadView('pdf.policies.pa.certificate', [
            'data' => $data
        ]);

        return $pdf->stream('certificate-' . ($data->policy ? $data->policy->document_no : 'no-document') . '.pdf');
    }
    public function exportInsuredPerson($id)
    {
        return $this->policyService->exportInsuredPerson($id);
    }
}
