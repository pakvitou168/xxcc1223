<?php

namespace App\Http\Controllers\Endorsement;

use App\Exports\InsuredPersonsExport;
use App\Http\Controllers\Controller;
use App\Models\BankInformation\BankInformation;
use App\Models\Travel\DataDetail;
use App\Models\Travel\DataMaster;
use App\Models\Travel\Policy\Insurance\Endorsement;
use App\Models\Travel\Policy\Insurance\EndorsementView;
use App\Models\Travel\Policy\Insurance\PolicyCommissionData;
use App\Models\Travel\Policy\Insurance\ReinsuranceData;
use App\Models\Travel\Policy\Invoice\InvoiceNote;
use App\Models\RefEnum;
use App\Models\ReinsuranceConfig\ReinsurancePartner;
use App\Models\UserManagement\User\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TravelEndorsementServiceController extends Controller
{
    CONST ERROR_MESSAGE = "Something went wrong!";
    public function __construct()
    {
    }
    public function showPolicyCancellationTab(EndorsementView $endorsement)
    {
        // If not policy cancellation
        if ($endorsement->travel->endorsement_type !== 'CANCELLATION')
            return false;

        // If all vehicles is deleted
        if ($endorsement->travel->travelDetails->isEmpty())
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

        $address = null;
        if ($endorsement->travel && $endorsement->travel->customer) {
            $travelInfo = $endorsement->travel->customer->travelInfo();
            if ($travelInfo && isset($travelInfo->address)) {
                $address = $travelInfo->address;
            }
        }
        $invoice->address = $address;

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
        $pdf->loadView('pdf.endorsements.travel.invoice', ['invoice' => $invoice, 'bank_list' => $bank_list, 'signature' => $signature ?? null]);
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
        $travel = $endorsement->travel;
        $travel->address = $travel->customer->info()->address;
        $travel->period_of_insurance = $travel->insuredPeriod();
        $travel->issued_by = $travel->issuedByName($travel->updated_by ?? $travel->created_by);
        $travel->endorsement_premium = DataDetail::whereMasterDataId($travel->id)->whereNotNull('endorsement_state')->sum('premium');
        return $travel;
    }

    public function printEndorsement(Request $request, EndorsementView $endorsement)
    {
        App::setLocale(strtolower($request->lang) ?? 'en');
        $data['letterhead'] = $request->letterhead;
        $travel = $this->coverDetail($endorsement);
        // $travel = json_decode(json_encode($travel),false);
        $travel->endorsement_premium = $endorsement->total_premium;
        $travel->endorsement_type = optional(RefEnum::listEndorsementTypes('TV_ENDORSEMENT_TYPE'))[preg_replace('/\s+/', '', $travel->endorsement_type)];
        $travel->end_description = str_replace("\n",'<br/>',$endorsement->endorsement_description); //backslash not working in snappy pdf
        $data['travel'] = $travel;
        $documentNo = $endorsement->document_no;
        $data['documentNo'] = $documentNo;

        if ($endorsement->status == 'APV') {
            $data['signature'] = UserFile::select('file_url')->where('user_id', $endorsement->approved_by)->first();
            if ($data['signature'])
                if (!$data['signature']['file_url'])
                    $data['signature'] = null;
        } else {
            $data['signature'] = null;
        }

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('title', 'PGI');
        $pdf->loadView('pdf.endorsements.travel.cover', $data);
        $pdf->setOption('page-size', 'a4');
        $pdf->setOption('disable-smart-shrinking', true);
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
        $pdf->loadView('pdf.endorsements.travel.credit', ['credit' => $credit, 'bank_list' => $bank_list, 'signature' => $signature ?? null]);

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
        $travelInvoice = DB::select("select * from ins_generate_policy_invoice_note(?,?,?)", $params);
        info('Invoice Generation', ['response' => $travelInvoice]);
        if (!empty($travelInvoice))
            return response([
                'success' => true,
                'message' => 'Generate Invoice Successfully!',
            ]);
        return response(self::ERROR_MESSAGE, 500);
    }

    public function generateCreditNote(Request $request)
    {
        $params = [
            $request->documentNo,
            $request->requestType,
            auth()->id(),
        ];
        $travelInvoice = DB::select("select * from ins_generate_policy_invoice_note(?,?,?)", $params);
        info('Invoice Generation', ['response' => $travelInvoice]);
        if (!empty($travelInvoice))
            return response([
                'success' => true,
                'message' => 'Generate Invoice Successfully!',
            ]);
        return response(self::ERROR_MESSAGE, 500);
    }
    public function canExportAll(Endorsement $endorsement)
    {
        return Endorsement::isLatestEndorsement($endorsement);
    }
}
