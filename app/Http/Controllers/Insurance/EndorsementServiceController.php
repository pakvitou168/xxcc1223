<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Models\Insurance\Endorsement\Endorsement;
use App\Models\RefEnum;
use Illuminate\Support\Facades\DB;
use App\Models\Insurance\AutoDetail;
use App\Models\Insurance\BasePolicy;
use Illuminate\Http\Request;
use App\Models\Insurance\ReinsuranceData;
use App\Models\Insurance\PolicyCommissionData;
use Illuminate\Support\Facades\App;
use App\Models\BankInformation\BankInformation;
use App\Models\UserManagement\User\UserFile;
use App\Models\Insurance\Invoice\InvoiceNote;
use App\Http\Controllers\Insurance\EndorsementController;
use App\Models\CoverPackage\CoverPackage;
use App\Models\Deductible\DeductibleDetail;
use App\Models\CustomerManagement\Country;
use App\Models\Address\AddressCode;
use App\Models\Insurance\Auto;
use App\Exports\VehiclesEndorsementExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EndorsementRegisterExport;
use App\Models\ReinsuranceConfig\ReinsurancePartner;

class EndorsementServiceController extends Controller
{
    public function listRefundTypeOptions() {
        return RefEnum::listRefundTypes();
    }

    public function showPolicyCancellationTab(Endorsement $endorsement) {
        // If not policy cancellation
        if ($endorsement->auto->endorsement_type !== 'CANCELLATION') return false;

        // If all vehicles is deleted
        if ($endorsement->auto->autoDetails->isEmpty()) return false;
        
        return true;
    }

    public function generateReinsuranceShare(Endorsement $endorsement) {
        $generated = AutoDetail::where('master_data_id', $endorsement->data_id)->where('endorsement_stage', $endorsement->document_no)->get()->map(function($item) use($endorsement) {
            $params = [
                $endorsement->id,
                $endorsement->data_id,
                $item->id,
            ];
            return DB::select("select * from ins_do_auto_endor_gen_reins_share(?,?,?)", $params);
        });

        if (!empty($generated)) {
            return response([
                'success' => true,
                'message' => 'Generated successfully!',
                'generated' => $generated
            ]);
        }
    }

    public function generateReinsuranceData(Endorsement $endorsement) {
        $generated = AutoDetail::where('master_data_id', $endorsement->data_id)->where('endorsement_stage', $endorsement->document_no)->get()->map(function($item) use($endorsement) {
            $params = [
                $endorsement->id,
                $endorsement->data_id,
                $item->id,
            ];
            return DB::select("select * from ins_do_auto_endor_gen_reins_data(?,?,?)", $params);
        });

        if (!empty($generated)) {
            return response([
                'success' => true,
                'message' => 'Generated successfully!',
                'generated' => $generated
            ]);
        }
    }

    public function checkCanExportVehicleListForAll(Endorsement $endorsement) {
        return BasePolicy::isLatestEndorsement($endorsement);
    }

    // Use approved_status as submit_status
    public function updateSubmitStatus(Endorsement $endorsement, Request $request){
        if($endorsement->approved_status !== $request->status){
            $endorsement->approved_status = $request->status;

            if ($endorsement->save()) {
                return [
                    'success' => true,
                    'message' => 'Endorsement submit status has been updated to '.$request->post('status')
                ];
            }
        }
    }

    public function isEndorsementReinsuranceCompleted(Endorsement $endorsement) {
        return ReinsuranceData::isReinsuranceCompleted($endorsement->id, $endorsement->document_no);
    }

    /**
     * Since deleted vehicles do not have reinsurance data, we check only newly added vehicles
     */
    public function hasEndorsedVehicles(Endorsement $endorsement){
        return AutoDetail::where('endorsement_stage', $endorsement->document_no)->first() ? true : false;
    }

    public function getCommissionData(Endorsement $endorsement) {
        return PolicyCommissionData::where('policy_id', $endorsement->id)
            ->whereHas('autoDetails', function($query) use($endorsement) {
                $query->where('endorsement_stage', $endorsement->document_no);
            })
            ->where('status', 'ACT')
            ->orderBy('detail_id')
            ->get();
    }

    public function isCommissionDataAvailable(Endorsement $endorsement){
        return PolicyCommissionData::where('policy_id', $endorsement->id)
            ->whereHas('autoDetails', function($query) use($endorsement) {
                $query->where('endorsement_stage', $endorsement->document_no);
            })
            ->where('status', 'ACT')
            ->first() ? true : false;
    }

    public function checkEndorsementHasNewVehicle(Endorsement $endorsement) {
        $autoDetails = AutoDetail::where('master_data_id', $endorsement->data_id)
        ->where('endorsement_state', 'ADDITION')
        ->where('endorsement_stage', $endorsement->document_no)
        ->count();

        return $autoDetails > 0;
    }

    public function downloadEndorsement(Endorsement $endorsement) {
        App::setLocale('en');

        $autoData = (new EndorsementController)->showDetail($endorsement);
        $autoData['hasLetterHead'] = request()->letterhead;

        $documentNo = $endorsement->document_no;
        $autoData['documentNo'] = $documentNo;

        if($endorsement->status == 'APV'){
            $autoData['signature'] = UserFile::select('file_url')->where('user_id', $endorsement->approved_by)->first();
            // fix error when there is a record but empty file_url
            if($autoData['signature'])
                if(!$autoData['signature']['file_url'])
                    $autoData['signature'] = null;
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

        $pdf->loadView('pdf.endorsements.auto', $autoData);

        return $pdf->stream($documentNo . '.pdf');
    }

    public function downloadInvoice($id, $withSignature = null)
    {
        $invoice = InvoiceNote::getInvoiceData($id);

        $bank_list = BankInformation::where('status', 'ACT')->where('default', true)->get();

        $policy = Endorsement::find($id);
        if($policy->status == 'APV' && $withSignature){
            $signature = UserFile::select('file_url')->where('user_id', $policy->approved_by)->where('file_type','SIGNATURE')->first();
            if($signature)
                if(!$signature->file_url)
                    $signature = null;
        }


        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->setOption('title', 'Invoice');
        $pdf->loadView('pdf.policies.invoice', ['invoice' => $invoice, 'bank_list' => $bank_list, 'signature' => $signature ?? null]);

        $pdf->setOption('page-size', 'a4');
        $pdf->setOption('disable-smart-shrinking', true);
        $pdf->setOption('margin-top', 36);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 15);
        $pdf->setOption('margin-right', 15);

        return $pdf->stream($invoice->inv_cdn_no.'.pdf');
    }

    public function downloadCreditNote($id, $withSignature = null)
    {
        $credit = InvoiceNote::getInvoiceData($id);

        $bank_list = BankInformation::where('status', 'ACT')->where('default', true)->get();

        $endorsement = Endorsement::find($id);
        if($endorsement->status == 'APV' && $withSignature){
            $signature = UserFile::select('file_url')->where('user_id', $endorsement->approved_by)->where('file_type','SIGNATURE')->first();
            if($signature)
                if(!$signature->file_url)
                    $signature = null;
        }

        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->setOption('title', 'Credit Note');
        $pdf->loadView('pdf.policies.credit', ['credit' => $credit, 'bank_list' => $bank_list, 'signature' => $signature ?? null]);

        $pdf->setOption('page-size', 'a4');
        $pdf->setOption('disable-smart-shrinking', true);
        $pdf->setOption('margin-top', 36);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 15);
        $pdf->setOption('margin-right', 15);

        return $pdf->stream($credit->inv_cdn_no.'.pdf');
    }

    public function downloadAutoCertificate($id) {
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('title', 'Certificate');

        $pdf->setOption('page-size', 'a4');
        $pdf->setOption('disable-smart-shrinking', true);
        $pdf->setOption('zoom', 1);
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-bottom', 0);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);

        $data = $this->getAutoCertificateData($id);

        $pdf->loadView('pdf.policies.certificate', $data);

        return $pdf->stream('certificate.pdf');
    }

    private function getAutoCertificateData($id) {
        $endorsement = Endorsement::select('id', 'document_no', 'data_id', 'version')->find($id);

        $auto = Auto::with(['customer' => function($query) {
                $query->select('customer_no', 'address_en', 'name_en','village_en','country_code', 'postal_code');
            }])
            ->select('id', 'customer_no', 'insured_name', 'effective_day', 'effective_date_from', 'effective_date_to')
            ->find($endorsement->data_id);

        $addressData = AddressCode::select('province', 'district', 'commune')->where('postal_code', $auto->customer->postal_code)->first();
        $auto->addressData = $addressData;

        $country = Country::select('description')->where('country_code', $auto->customer->country_code)->value('description');
        $auto->country = $country;

        $autoDetails = AutoDetail::with(['makeModel' => function($query) {
                $query->with(['make' => function($query) {$query->select('id', 'make');}])
                    ->select('id', 'model', 'make_id');
            }])
            ->select(
                'id',
                'product_code',
                'master_data_id',
                'plate_no',
                'model_id',
                'chassis_no',
                'engine_no',
                'selected_cover_pkg',
                'cover_pkg_id',
                'endorsement_e_date',
                'endos_day_remaining'
            )
            ->where('master_data_id', $auto->id)
            ->where('endorsement_state', 'ADDITION')
            ->where('endorsement_stage', $endorsement->document_no)
            ->orderBy('id')
            ->get();

        // If has no vehicle for certificate
        if ($autoDetails->isEmpty()) abort('404');

        $autoDetails = $autoDetails->map(function($item) {
            $deductibles = DeductibleDetail::listByDetailAndProduct($item->id, $item->product_code);
            $item->deductibles = $deductibles;

            $coverArr = explode(',', $item->selected_cover_pkg);

            $covers = CoverPackage::getCoverPackageWithRemainingCovers($item->cover_pkg_id, $coverArr);
            $item->covers = $covers;

            return $item;
        });

        return [
            'policy' => $endorsement,
            'auto' => $auto,
            'auto_details' => $autoDetails
        ];
    }

    public function exportVehicles($id, $document_no, $endorsement_type) {
        $isGeneralInfoVehicle = $endorsement_type == 'GENERAL';
        if($isGeneralInfoVehicle)
            $isListForAll = true;
        else
            $isListForAll = false;
        return Excel::download(new VehiclesEndorsementExport($id, $document_no, $isListForAll,$isGeneralInfoVehicle), $document_no.'.xlsx');
    }

    public function exportVehicleListsForAll($id) {
        $latestEndorsement = BasePolicy::find($id);

        $policy = BasePolicy::select('id', 'quotation_id', 'document_no')
            ->where('quotation_id', $latestEndorsement->quotation_id)
            ->whereNull('version')
            ->first();

        return Excel::download(new VehiclesEndorsementExport($latestEndorsement->data_id, $policy->document_no, true), $policy->document_no.'.xlsx');
    }

    public function exportEndorsements(Request $request){
        return Excel::download(new EndorsementRegisterExport($request->route('issue_date_from'), $request->route('issue_date_to'),ReinsurancePartner::getAllPartnerNames()), 'Endorsement.xlsx');
    }

    public function getValidEndorsementDatePeriod($id)
    {
        $policy = BasePolicy::find($id);
        $auto = Auto::find($policy->data_id);
        $data = [
            'from' => $auto->effective_date_from,
            'to' => $auto->effective_date_to,
        ];
        return $data;
    }
}
