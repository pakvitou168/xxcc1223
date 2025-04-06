<?php

namespace App\Services\PA;

use App;
use App\Exceptions\InsException;
use App\Exports\PA\InsuredPersonExport;
use App\Models\PA\Commission;
use App\Models\PA\DataMaster;
use App\Models\PA\Policy;
use App\Models\PA\PolicyV;
use App\Models\RecordStatus;
use App\Models\UserManagement\User\UserFile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB as FacadesDB;
use KhmerDateTime\KhmerDateTime;
use Maatwebsite\Excel\Facades\Excel;

class PolicyService
{
    const DATA_TYPE = 'POLICY';

    public function __construct(private CalculationService $calculationService, private ReinsuranceService $reinsuranceService, private EndorsementService $endorsementService)
    {
    }

    public function list()
    {
        return PolicyV::query();
    }

    public function detail($id, $lang = 'en'): DataMaster
    {
        $isEN = $lang === 'en';
        $data = DataMaster::with([
            'endorsement' => fn($q) => $q->select('previous_id'),
            'policy' => fn($q) => $q->select(
                'data_id',
                'document_no',
                'policy_no',
                'status',
                'created_by',
                'updated_by',
                'id',
                'approved_by',
                'status',
                'approved_status'
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
                'customer_no',
                'coverage_id',
                'total_premium',
                'updated_at',
                'accumulation_limit_amount'
            )
            ->findOr($id, fn() => throw new ModelNotFoundException("Policy not found", 404));

        $data->customer->address = $data->customer?->correspondence_address;
        $data->insurance_period = $this->insurancePeriod($data, $lang);
        $data->clauses = $data->insuranceClauses->groupBy('clause_type');
        $data->jurisdiction = trans('Kingdom of Cambodia', [], $lang);
        $data->total_insured_persons = DataMaster::withCount(['dataDetails' => fn($q) => $q->whereStatus(RecordStatus::ACTIVE)])->find($id)->data_details_count;

        // In PolicyService.php, modify the issued_on line to include a null check
        $data->issued_on = $data->updated_at
            ? ($lang == 'en'
                ? Carbon::parse($data->updated_at)->format('d F Y')
                : KhmerDateTime::parse($data->updated_at)->format('LL'))
            : ($lang == 'en' ? 'Not Available' : 'មិនមាន'); //NOSONAR
        $data->signature = $this->signature($data->policy);

        $data->coverage->name = $isEN ? $data->coverage->name : $data->coverage->name_km;
        $data->makeHidden('insuranceClauses');
        $data->coverage->makeHidden('name_km');
        return $data;
    }
    public function commission($id)
    {
        $commission = Commission::whereDataId($id)->with('business')->whereStatus(RecordStatus::ACTIVE)->first();
        $commission->business_name = $commission->business->full_name;
        return $commission->makeHidden('business');
    }
    public function updateCommission($data, $id)
    {
        Commission::whereDataId($id)->update($data);
    }
    private function signature($policy)
    {
        return UserFile::whereFileType('SIGNATURE')->whereUserId($policy->approved_by)->value('file_url');
    }
    private function insurancePeriod($master, $lang = 'en')
    {
        $from = $lang == 'en' ? Carbon::parse($master->effective_date_from)->format('d/F/Y') : KhmerDateTime::parse($master->effective_date_from)->format('LL');
        $to = $lang == 'en' ? Carbon::parse($master->effective_date_to)->format('d/F/Y') : KhmerDateTime::parse($master->effective_date_to)->format('LL');
        return $master->effective_day . ' ' . trans('Days', [], $lang) . ' - ' . trans('From', [], $lang) . ' ' . $from . ' ' . trans('To', [], $lang) . " $to  (" . trans('Both Days Inclusive', [], $lang) . ')';
    }
    public function configuration($id)
    {
        return Policy::whereDataId($id)->select('business_type', 'policy_type', 'id')->firstOr(fn() => throw new ModelNotFoundException("Policy not found"));
    }
    public function updateConfig($data, $id)
    {
        Policy::whereDataId($id)->update($data);
    }
    private function reinsuranceData($id)
    {
        return $this->reinsuranceService->detail($id);
    }
    public function reinsurance($params, $id)
    {
        $policy = Policy::whereDataId($id)->firstOr(fn() => throw new ModelNotFoundException("Policy not found"));
        $reinsurance = $this->reinsuranceData($id);
        if (count($reinsurance) === 0 && $params->generate) {
            $this->reinsuranceService->generateShare($policy->id);
            $this->reinsuranceService->generateData($policy->id);
            return $this->reinsuranceData($id);
        }
        return $reinsurance;
    }
    public function edit($id)
    {
        return Policy::whereDataId($id)->firstOr(fn() => throw new ModelNotFoundException("Policy not found"));
    }
    public function delete($id)
    {
        Policy::whereDataId($id)->update([
            'status' => RecordStatus::DELETED
        ]);
    }
    public function updateReinsurance($data, $id)
    {
        $reinsurances = collect($data);
        $presentedItems = $reinsurances->whereNull('deleted_at');
        $totalShare = $presentedItems->sum(fn($item) => $item['share']);
        if ($totalShare > 100) {
            throw new InsException("Share is over the limitation of 100%");
        }

        $master = DataMaster::findOr($id, fn() => throw new ModelNotFoundException("Policy not found"));
        $deletedItems = $reinsurances->whereNotNull('deleted_at')->pluck('id')->toArray();
        $newItems = $reinsurances->whereNull('id');
        $updatedItems = $reinsurances->whereNull('deleted_at')->whereNotNull('id');

        if (count($deletedItems)) {
            $this->reinsuranceService->delete($deletedItems);
        }
        if ($newItems->count()) {
            $this->reinsuranceService->save($newItems, $master);
        }
        if ($updatedItems->count()) {
            $this->reinsuranceService->update($updatedItems);
        }
        $this->reinsuranceService->generateData($master->policy->id);
        if ($this->isReadyForSubmit($master)) {
            $this->proceedToSubmit($master);
        }
    }
    public function approve($data, $id)
    {
        $policy = Policy::whereDataId($id)->firstOr(fn() => throw new ModelNotFoundException("Policy not found"));
        if ($policy->status !== RecordStatus::PENDING) {
            throw new InsException("Policy has already been approved, please refresh page");
        }
        $policy->update([
            'status' => $data['status'],
            'approved_reason' => $data['remark'],
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);
        if ($policy->status === RecordStatus::APPROVED) {
            $bindings = [
                $policy->document_no,
                'INVOICE',
                auth()->id()
            ];
            info("master", ['updated_at' => DataMaster::find($id)->updated_at]);
            $this->calculationService->generateInvoice($bindings);
            return $policy;
        }
    }
    public function endorse($data, $id)
    {
        $master = DataMaster::with('policy')->findOr($id, fn() => throw new ModelNotFoundException("Policy not found"));
        if ($master->endorsement) {
            throw new InsException("Policy already has an endorsement, cannot generate new", 500);
        }
        return $this->endorsementService->generate($master->policy->id, $data);
    }
    private function isReadyForSubmit($master)
    {
        return $master->policy->business_type && $master->policy->policy_type && $master->policy->status === RecordStatus::PENDING;
    }
    private function proceedToSubmit($master)
    {
        $master->policy->update([
            'approved_status' => RecordStatus::SUBMITTED
        ]);
        $master->updated_at = now();
        $master->save();
    }

    public function getInsuredPersons($id)
    {
        $data = $this->detail($id);
        return $data->insuredPersons;
    }
    public function exportInsuredPerson($id)
    {
        $master = DataMaster::findOr($id, fn() => throw new ModelNotFoundException("Quotation not found"));
        return Excel::download(new InsuredPersonExport($master), 'policy-insured-persons.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    public function getInvoiceData($id)
    {
        try {
            // Instead of looking for policy with the ID, find it by data_id
            $policy = Policy::with('dataMaster')->where('data_id', $id)->firstOrFail();

            // Create invoice object with policy data
            $invoice = new \stdClass();
            $invoice->invoice_no = 'INV-' . $policy->document_no;
            $invoice->issue_date = $policy->updated_at ?? $policy->created_at;
            $invoice->code = $policy->dataMaster->business_code;
            $invoice->customer_name = $policy->dataMaster->insured_name;
            $invoice->policy_document_no = $policy->document_no;
            $invoice->total_premium = $policy->dataMaster->total_premium;
            $invoice->exch_rate = 4100; // Default exchange rate

            // Get latest exchange rate if available
            try {
                $latestRate = FacadesDB::table('fin_exchange_rate')
                    ->where('status', 'ACT')
                    ->where('ccy_code', 'USD')
                    ->orderBy('effective_date', 'desc')
                    ->first();

                if ($latestRate) {
                    $invoice->exch_rate = $latestRate->rate;
                }
            } catch (\Exception $e) {
                // Use default rate if error
            }

            return $invoice;
        } catch (\Exception $e) {
            // If policy not found, create invoice from data master directly
            $data = $this->detail($id);

            $invoice = new \stdClass();
            $invoice->invoice_no = 'INV-' . ($data->policy?->document_no ?? 'N/A');
            $invoice->issue_date = $data->updated_at ?? now();
            $invoice->code = $data->business_code ?? 'N/A';
            $invoice->customer_name = $data->insured_name ?? 'N/A';
            $invoice->policy_document_no = $data->policy?->document_no ?? 'N/A';
            $invoice->total_premium = $data->total_premium ?? 0;
            $invoice->exch_rate = 4100; // Default exchange rate

            return $invoice;
        }
    }
}
