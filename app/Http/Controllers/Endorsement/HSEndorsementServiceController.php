<?php

namespace App\Http\Controllers\Endorsement;

use App\Exports\InsuredPersonsExport;
use App\Http\Controllers\Controller;
use App\Models\BankInformation\BankInformation;
use App\Models\HS\DataDetail;
use App\Models\HS\DataMaster;
use App\Models\HS\Insurance\Endorsement;
use App\Models\HS\Insurance\EndorsementView;
use App\Models\HS\Insurance\PolicyCommissionData;
use App\Models\HS\Insurance\ReinsuranceData;
use App\Models\HS\Invoice\InvoiceNote;
use App\Models\RefEnum;
use App\Models\ReinsuranceConfig\ReinsurancePartner;
use App\Models\UserManagement\User\UserFile;
use App\Services\HS\PolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HSEndorsementServiceController extends Controller
{

    public function __construct(private PolicyService $policyService)
    {
    }
    public function showPolicyCancellationTab(EndorsementView $endorsement)
    {
        // If not policy cancellation
        if ($endorsement->hs->endorsement_type !== 'CANCELLATION')
            return false;

        // If all vehicles is deleted
        if ($endorsement->hs->hsDetails->isEmpty())
            return false;
        return true;
    }

    public function getCommissionData(EndorsementView $endorsement)
    {
        return PolicyCommissionData::where('policy_id', $endorsement->id)
            ->where('status', 'ACT')
            ->orderBy('policy_id')
            ->get();
    }

    public function exportInsuredPersons($id, $document_no)
    {
        $dataMaster = DataMaster::findOrFail($id);
        return Excel::download(new InsuredPersonsExport($dataMaster->id, $document_no, $dataMaster->endorsement_type == 'GENERAL', $dataMaster->endorsement_type == 'GENERAL'), $document_no . '.xlsx');
    }

    public function exportAllInsuredPersons($id, $document_no)
    {
        return Excel::download(new InsuredPersonsExport($id, $document_no, true), $document_no . '.xlsx');
    }

    public function isCommissionDataAvailable(EndorsementView $endorsement)
    {
        return PolicyCommissionData::where('policy_id', $endorsement->id)
            ->where('status', 'ACT')
            ->first() ? true : false;
    }


    public function getReinsuranceData(EndorsementView $endorsement)
    {
        return ReinsuranceData::where('policy_id', $endorsement->id)
            ->where('lvl', 1)
            ->where('status', 'ACT')
            ->orderBy('id')
            ->get()
            ->map(function ($item) use ($endorsement) {
                $subReinsuranceData = ReinsuranceData::where('policy_id', $endorsement->id)
                    ->where('data_id', $item->data_id)
                    ->where('parent_code', $item->treaty_code)
                    ->where('lvl', 2)
                    ->where('status', 'ACT')
                    ->orderBy('id')
                    ->get()
                    ->map(function ($item) {
                        $item->participant = ReinsurancePartner::getPartnerNameByCode($item->treaty_code);
                        return $item;
                    });

                $item->sub_reinsurance_data = $subReinsuranceData;
                $item->reinsurance_type = ReinsuranceData::getReinsuranceType($item->product_code, $item->treaty_code);
                $item->participant = ReinsurancePartner::getPartnerNameByCode($item->treaty_code);
                $item->endorsement_type = $endorsement->endorsement_type;
                return $item;
            });
    }

    public function listRefundTypeOptions($policyId)
    {
        $refundTypeOptions = DB::select("select * from ins_hs_get_refund_options(?)", [$policyId]);
        return collect($refundTypeOptions)->map(function ($item) {
            return [
                'label' => $item->name,
                'value' => $item->enum_id
            ];
        });
    }

    public function printInvoice(Request $request, Endorsement $endorsement)
    {
        $invoice = InvoiceNote::getInvoiceData($endorsement->id);
        $invoice->address = @$endorsement->hs->customer->info()->address;
        $withSignature = $request->signature ?? 0;
        $bank_list = BankInformation::where('status', 'ACT')->where('default', true)->get();

        if ($endorsement->status == 'APV' && $withSignature) {
            $signature = UserFile::select('file_url')->where('user_id', $endorsement->approved_by)->where('file_type', 'SIGNATURE')->first();
            if ($signature)
                if (!$signature->file_url)
                    $signature = null;
        }
        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->setOption('title', 'Invoice');
        $pdf->loadView('pdf.endorsements.hs.invoice', ['invoice' => $invoice, 'bank_list' => $bank_list, 'signature' => $signature ?? null]);

        $pdf->setOption('page-size', 'a4');
        $pdf->setOption('disable-smart-shrinking', true);
        $pdf->setOption('margin-top', 33);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 15);
        $pdf->setOption('margin-right', 15);
        return $pdf->stream($invoice->inv_cdn_no . '.pdf');
    }

    private function coverDetail(EndorsementView $endorsement)
    {
        $hs = $endorsement->hs;
        $hs->address = $hs->customer->info()->address;
        $hs->period_of_insurance = $hs->insuredPeriod();
        $hs->issued_by = $hs->issuedByName($hs->updated_by ?? $hs->created_by);
        $hs->endorsement_premium = DataDetail::whereMasterDataId($hs->id)->whereNotNull('endorsement_state')->sum('premium');
        return $hs;
    }

    public function printEndorsement(Request $request, EndorsementView $endorsement)
    {

        App::setLocale(strtolower($request->lang) ?? 'en');
        $hsData['letterhead'] = $request->letterhead;
        $hs = $this->coverDetail($endorsement);
        // $hs = json_decode(json_encode($hs),false);
        $hs->endorsement_premium = $endorsement->total_premium;
        $hs->endorsement_type = optional(RefEnum::listEndorsementTypes('HS_ENDORSEMENT_TYPE'))[preg_replace('/\s+/', '', $hs->endorsement_type)];
        $hs->end_description = str_replace("\n",'<br/>',$endorsement->endorsement_description); //backslash not working in snappy pdf
        $hsData['hs'] = $hs;
        $documentNo = $endorsement->document_no;
        $hsData['documentNo'] = $documentNo;

        if ($endorsement->status == 'APV') {
            $hsData['signature'] = UserFile::select('file_url')->where('user_id', $endorsement->approved_by)->first();
            if ($hsData['signature'])
                if (!$hsData['signature']['file_url'])
                    $hsData['signature'] = null;
        } else {
            $hsData['signature'] = null;
        }

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('title', 'PGI');
        $pdf->setOption('margin-top', 36);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOptions([
            'header-html' => view('pdf.header', [
                'documentNo' => $documentNo,
                'hasLetterHead' => request()->letterhead
            ]),
            'footer-html' => view('pdf.footer', [
                'hasLetterHead' => request()->letterhead
            ]),
        ]);
        $pdf->loadView('pdf.endorsements.hs.cover', $hsData);
        return $pdf->stream($documentNo . '.pdf');
    }

    public function printCreditNote(Request $request, Endorsement $endorsement)
    {
        $credit = InvoiceNote::getInvoiceData($endorsement->id);
        $withSignature = $request->signature ?? 0;
        $bank_list = BankInformation::where('status', 'ACT')->where('default', true)->get();
        if ($endorsement->status == 'APV' && $withSignature) {
            $signature = UserFile::select('file_url')->where('user_id', $endorsement->approved_by)->where('file_type', 'SIGNATURE')->first();
            if ($signature)
                if (!$signature->file_url)
                    $signature = null;
        }

        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->setOption('title', 'Credit Note');
        $pdf->loadView('pdf.endorsements.hs.credit', ['credit' => $credit, 'bank_list' => $bank_list, 'signature' => $signature ?? null]);

        $pdf->setOption('page-size', 'a4');
        $pdf->setOption('disable-smart-shrinking', true);
        $pdf->setOption('margin-top', 33);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 15);
        $pdf->setOption('margin-right', 15);

        return $pdf->stream($credit->inv_cdn_no . '.pdf');
    }

    public function generateInvoice(Request $request)
    {
        $params = [
            $request->documentNo,
            $request->requestType,
            auth()->id(),
        ];
        $hsInvoice = DB::select("select * from ins_generate_policy_invoice_note(?,?,?)", $params);
        info('Invoice Generation', ['response' => $hsInvoice]);
        if (!empty($hsInvoice))
            return response([
                'success' => true,
                'message' => 'Generate Invoice Successfully!',
            ]);
        return response('Something went wrong!', 500);
    }

    public function generateCreditNote(Request $request)
    {
        $params = [
            $request->documentNo,
            $request->requestType,
            auth()->id(),
        ];
        $hsInvoice = DB::select("select * from ins_generate_policy_invoice_note(?,?,?)", $params);
        info('Invoice Generation', ['response' => $hsInvoice]);
        if (!empty($hsInvoice))
            return response([
                'success' => true,
                'message' => 'Generate Invoice Successfully!',
            ]);
        return response('Something went wrong!', 500);
    }
    public function canExportAll(Endorsement $endorsement)
    {
        return Endorsement::isLatestEndorsement($endorsement);
    }
}
