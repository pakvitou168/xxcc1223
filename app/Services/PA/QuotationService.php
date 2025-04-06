<?php

namespace App\Services\PA;

use App;
use App\Exceptions\InsException;
use App\Exports\PA\InsuredPersonExport;
use App\Models\Insurance\InsuranceClause;
use App\Models\PA\Coverage;
use App\Models\PA\DataDetail;
use App\Models\PA\DataMaster;
use App\Models\PA\Quotation;
use App\Models\PA\QuotationV;
use App\Models\RecordStatus;
use App\Models\UserManagement\User\UserFile;
use Arr;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use KhmerDateTime\KhmerDateTime;
use Log;
use Maatwebsite\Excel\Facades\Excel;

class QuotationService
{
    const DATA_TYPE = 'QUOTATION';
    const JOINT = 'J';
    private $syncService;
    private $calcService;
    public function __construct()
    {
        $this->syncService = new SyncService(self::DATA_TYPE);
        $this->calcService = new CalculationService();
    }
    public function list()
    {
        return QuotationV::query();
    }
    public function save($data)
    {
        $assigments = $this->fillables($data);
        $master = DataMaster::create($assigments);

        if ($data['joint_status'] === self::JOINT) {
            $this->syncService->syncJointDetails($master, $data['joint_details']);
        }
        $this->syncService->syncClauses($master, [...$data['endorsement_clauses'], ...$data['general_exclusions'], ...$data['automatic_extensions']]);
        $this->syncService->syncInsuredPersons($master, $data['file']);
        $this->syncService->syncExtensions($master, $data['optional_benefits']);

        if ($this->calcService->generatePremium([$master->id,auth()->id()])) {
            $this->generateQuotation($master);
        }
    }

    private function generateQuotation($master)
    {
        $generateQuote = DB::select("select  * from ins_pa_prod_generate_new_quotation(?,?)", [$master->id, auth()->id()]);
        info("Generate PA Quotation.", ['binding' => json_encode([$master->id, auth()->id()]), 'data' => json_encode($generateQuote)]);
        $result = collect($generateQuote)->first();
        if (!$result || $result->response_code !== 200) {
            Log::error('Generating PA quote failed: ', ['result' => json_encode($generateQuote)]);
            throw new InsException("Generating quotation failed", 500);
        }
        return true;
    }
    

    private function fillables($data)
    {
        $coverage = Coverage::findOr($data['coverage_id'], fn() => throw new ModelNotFoundException("Geopgraphical limit not found"));
        $assigments = Arr::except($data, ['file', 'endorsement_clauses', 'general_exclusions', 'joint_details']);
        return [
            ...$assigments,
            'status' => RecordStatus::ACTIVE,
            'coverage_name' => $coverage->name,
            'data_type' => self::DATA_TYPE,
            'branch_code' => auth()->user()->branch_id
        ];
    }
    public function delete($id)
    {
        return DataMaster::whereId($id)->update([
            'status' => RecordStatus::DELETED
        ]);
    }
    public function edit($id): DataMaster
    {
        $data = DataMaster::with('insuranceClauses', 'quotation')->findOr($id, fn() => throw new ModelNotFoundException("Quote not found", 404));
        $data->customer_type = $data->customer->customer_type;
        $data->joint_details = $data->jointAccountDetails->map(function ($item) {
            $item->customer_type = $item->customer->customer_type;
            return $item->makeHidden('customer');
        });
        $data->endorsement_clauses = $data->insuranceClauses->where('clause_type', InsuranceClause::ENDORSEMENT_CLAUSE)->pluck('id')->toArray();
        $data->general_exclusions = $data->insuranceClauses->where('clause_type', InsuranceClause::GENERAL_EXCLUSION)->pluck('id')->toArray();
        $data->automatic_extensions = $data->insuranceClauses->where('clause_type', InsuranceClause::AUTOMATIC_EXT)->pluck('id')->toArray();
        return $data->makeHidden(['customer', 'jointAccountDetails', 'insuranceClauses']);
    }
    public function update(array $data, $id)
    {
        $assigments = $this->fillables($data);
        $master = DataMaster::findOr($id, fn() => throw new ModelNotFoundException("Quotation not found"));
        $master->update($assigments);
        if (isset($data['joint_details'])) {
            $this->syncService->syncJointDetails($master, $data['joint_details']);
        }

        $this->syncService->syncClauses($master, [...$data['endorsement_clauses'], ...$data['general_exclusions'], ...$data['automatic_extensions']]);
    }
    public function insuredPersonList($id)
    {
        return DataDetail::whereMasterDataId($id)->whereStatus(RecordStatus::ACTIVE);
    }
    public function detail($id, $lang = 'en'): DataMaster
    {
        $isEN = $lang === 'en';
        $data = DataMaster::withCount('dataDetails')
            ->with([
                'quotation' => fn($q) => $q->with(['policies' => fn($q) => $q->where('status', '<>', RecordStatus::DELETED)->select('quotation_id')])->select(
                    'data_id',
                    'document_no',
                    'approved_status',
                    'accepted_status',
                    'approved_by',
                    'created_by',
                    'updated_by',
                    'id'
                ),
                'product' => fn($q) => $q->select(
                    'code',
                    $isEN ? 'name' : 'name_kh AS name',
                    $isEN ? 'coverage_en AS coverage' : 'coverage_kh AS coverage',
                ),
                'customer' => fn($q) => $q->with([
                    'classification' => fn($q1) => $q1->select(
                        $isEN ? 'description AS occupation' : 'description_kh AS occupation',
                        'cust_classification'
                    )
                ])
                    ->select(
                        'customer_no',
                        $isEN ? 'name_en AS name' : 'name_kh AS name',
                        'cust_classification'
                    ),
                'optionalExtensions' => fn($q) => $q->whereIsSelected(true)->whereLangCode(strtoupper($lang))->select('extension_name', 'extension_description', 'data_id'),
                'insuredPersons' => fn($q) => $q->whereLangCode(strtoupper($lang))->select(
                    'insured_person',
                    'accidental_death',
                    'permanent_disablement',
                    'medical_expense',
                    'premium_per_person',
                    'premium',
                    'accidental_death_value',
                    'permanent_disablement_value',
                    'medical_expense_value',
                    'premium_per_person_value',
                    'premium_value',
                    'data_id'
                ),
                'coverage' => fn($q) => $q->select('id', 'name'),
                'insuranceClauses' => fn($q) => $q->select($isEN ? 'clause AS name' : 'clause_kh AS name', 'clause_type', 'code')
            ])
            ->select(
                'id',
                'business_code',
                'product_code',
                'branch_code',
                $isEN ? 'insured_name' : 'insured_name_kh AS insured_name',
                $isEN ? 'warranty' : 'warranty_kh AS warranty',
                $isEN ? 'memorandum' : 'memorandum_kh AS memorandum',
                $isEN ? 'subjectivity' : 'subjectivity_kh AS subjectivity',
                'policy_wording_version',
                'effective_date_from',
                'effective_date_to',
                'effective_day',
                $isEN ? 'insured_person_note' : 'insured_person_note_kh AS insured_person_note',
                $isEN ? 'remark' : 'remark_kh AS remark',
                'sale_channel',
                'customer_no',
                'coverage_id',
                'total_premium',
                'updated_at',
                'accumulation_limit_amount'
            )
            ->findOr($id, fn() => throw new ModelNotFoundException("Quote not found", 404));
        $data->customer->address = $data->customer?->correspondence_address;
        $data->insurance_period = $this->insurancePeriod($data, $lang);
        $data->clauses = $data->insuranceClauses->groupBy('clause_type');
        $data->jurisdiction = trans('Kingdom of Cambodia', [], $lang);
        $data->quotation_validity = trans('30 days from the issuance date', [], $lang);

        $data->issued_on = $lang == 'en' ? Carbon::parse($data->updated_at)->format('d F Y') : KhmerDateTime::parse($data->updated_at)->format('LL');
        $data->signature = $this->signature($data->quotation);

        $data->coverage->name = $isEN ? $data->coverage->name : $data->coverage->name_km;

        $data->makeHidden('insuranceClauses');
        $data->coverage->makeHidden('name_km');
        $data->total_insured_persons = DataMaster::withCount(['dataDetails' => fn($q) => $q->whereStatus(RecordStatus::ACTIVE)])->find($id)->data_details_count;
        return $data;
    }
    public function approve($id, $data)
    {
        $quote = Quotation::whereDataId($id)->firstOr(fn() => throw new ModelNotFoundException("Quotation not found"));
        if ($quote->created_by == auth()->id()) {
            throw new InsException("You can not approve your own quotation", 403);
        }

        $quote->update([
            'approved_status' => $data['status'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approved_reason' => $data['remark'],
            ...($data['status'] === RecordStatus::APPROVED ? ['accepted_status' => RecordStatus::PENDING] : [])
        ]);
        return $quote;
    }
    public function accept($id, $data)
    {
        $quote = Quotation::whereDataId($id)->firstOr(fn() => throw new ModelNotFoundException("Quotation not found"));
        if ($quote->created_by == auth()->id()) {
            throw new InsException("You can not accept your own quotation", 403);
        }
        $quote->update([
            'accepted_status' => $data['status'],
            'accepted_by' => auth()->id(),
            'accepted_at' => now(),
            'accepted_reason' => $data['remark']
        ]);
        return $quote;
    }
    public function proceed($id): bool
    {
        $quotation = Quotation::whereDataId($id)->firstOr(fn() => throw new ModelNotFoundException("Quotation not found"));
        $bindings = [$quotation->id, auth()->id()];
        $query = DB::select('select * from ins_pa_prod_generate_new_policy(?,?)', $bindings);
        $result = collect($query)->first();
        if (!$result || $result->response_code !== 200) {
            Log::error("Failed to proceed quotation no. $quotation->document_no", ['bindings' => json_encode($bindings), 'result' => json_encode($query)]);
            throw new InsException("Proceeding to policy failed", 500);
        }
        return true;
    }
    public function exportInsuredPerson($id)
    {
        $master = DataMaster::findOr($id, fn() => throw new ModelNotFoundException("Quotation not found"));
        return Excel::download(new InsuredPersonExport($master), 'quotation-insured-persons.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    public function print($id, $lang = 'en')
    {
        $letterHead = request()->letterhead;
        $data = $this->detail($id, $lang);
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('title', 'PA Quotation');

        $pdf->setOption('margin-top', 36);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('footer-right', 'Page: [page] of [topage]          ');
        $pdf->setOption('footer-font-size', 8);
        $pdf->setOptions([
            'header-html' => view('pdf.header', [
                'documentNo' => $data->quotation?->document_no,
                'hasLetterHead' => $letterHead
            ]),
            'footer-html' => view('pdf.footer', [
                'hasLetterHead' => $letterHead
            ]),
        ]);
        $pdf->loadView('pdf.quotations.pa', ['data' => $data, 'lang' => $lang]);

        return $pdf->stream("pa_quotation-" . $data->quotation?->document_no . ".pdf");
    }
    private function signature($quote)
    {
        return UserFile::whereFileType('SIGNATURE')->whereUserId($quote->approved_by)->value('file_url');
    }
    private function insurancePeriod($master, $lang = 'en')
    {
        $from = $lang == 'en' ? Carbon::parse($master->effective_date_from)->format('d/F/Y') : KhmerDateTime::parse($master->effective_date_from)->format('LL');
        $to = $lang == 'en' ? Carbon::parse($master->effective_date_to)->format('d/F/Y') : KhmerDateTime::parse($master->effective_date_to)->format('LL');
        return $master->effective_day . ' ' . trans('Days', [], $lang) . ' - ' . trans('From', [], $lang) . ' ' . $from . ' ' . trans('To', [], $lang) . " $to  (" . trans('Both Days Inclusive', [], $lang) . ')';
    }
}