<?php

namespace App\Services\Claim\HS;

use App\Models\HS\Claim\ClaimableHSPolicyV;
use App\Models\HS\Claim\ClaimHistoryV;
use App\Models\HS\Claim\ClaimRegister;
use App\Models\HS\Claim\ClaimRegisterDetail;
use App\Models\HS\Claim\ClaimRegisterV;
use App\Models\HS\Claim\ClaimSchemaData;
use App\Models\HS\Clinic\Clinic;
use App\Models\HS\DataDetailView;
use App\Models\HS\Policy;
use DB;
use Exception;
use Illuminate\Support\Carbon;
use Log;
class HSRegisterService
{
    public function __construct(private ClaimRegister $claimRegister, private ClaimRegisterV $claimRegisterV)
    {
    }

    public function policyList($search = null)
    {
        return ClaimableHSPolicyV::when($search, function ($q) use ($search) {
            $q->where('document_no', 'ILIKE', $search . "%")->orWhere('policy_no', 'ILIKE', $search . "%");
        })->limit(100)->select('document_no AS name', 'policy_id AS code')->get()->values();
    }

    public function clinicList()
    {
        return Clinic::whereStatus('ACT')->select('id AS code', 'name', 'type')->get();
    }

    public function insuredPersonsByPolicy()
    {
        $search = request()->search;
        $policy = Policy::findOr(request()->policy_id, fn() => throw new Exception("Incorrect policy number"));
        return DataDetailView::whereMasterDataId($policy->data_id)
            ->whereLangCode('EN')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'ILIKE', $search . "%");
            })
            ->where(function ($q) {
                $q->whereNull('endorsement_state')->orWhere('endorsement_state', '<>', 'DELETION');
            })
            ->select('name', 'id as code', 'gender', 'date_of_birth as dob')->limit(100)->get();
    }

    public function create($data): ClaimRegister
    {
        $policy = Policy::findOr($data->policy_id, fn() => throw new Exception("Incorrect policy number"));
        $params = [
            $policy->document_no,
            '',
            'CLAIM',
            auth()->id()
        ];
        $query = DB::select('select * from ins_hs_generate_claim_or_payment_no(?,?,?,?)', $params);
        if ($query[0] && $query[0]->response_code != 200) {
            throw new Exception($query[0]->response_message);
        }
        $claim = @$query[0]->generate_no ? $query[0] : throw new Exception("Generate claim no failed");
        $insuredPerson = DataDetailView::findOr($data->data_detail_id, fn() => throw new Exception("Insured person not found"));

        $register = $this->claimRegister->create([
            'seq_no' => $claim->seq,
            'claim_no' => $claim->generate_no,
            'policy_id' => $policy->id,
            'data_id' => $policy->data_id,
            'data_detail_id' => $data->data_detail_id,
            'insured_person_uuid' => $insuredPerson->insured_person_uuid,
            'cause_of_loss' => $data->cause_of_loss,
            'cause_of_loss_disability' => $data->cause_of_loss_disability,
            'date_of_loss' => $data->date_of_loss,
            'location_of_loss' => $data->location_of_loss,
            'loss_description' => $data->loss_description,
            'reserve_amount' => $data->reserve_amount,
            'clinic_id' => $data->clinic_id,
            'notification_date' => $data->notification_date,
            'schema_plan' => $data->schema_plan,
            'schema_type' => $data->schema_type,
            'schema_detail_code' => $data->schema_detail_code,
            'insured_period_from' => $policy->dataMaster->effective_date_from,
            'insured_period_to' => $policy->dataMaster->effective_date_to
        ]);
        return $register;
    }

    public function detail($id)
    {
        $claim = $this->claimRegisterV->findOr($id, fn() => throw new Exception("Claim not found"));
        $claim->insuredPerson->age = ceil($claim->insuredPerson->age);
        $claim->load('reinsurances');
        return $claim;
    }

    public function edit($id)
    {
        $claim = $this->claimRegisterV->findOr($id, fn() => throw new Exception("Claim not found"));
        $policy = new \stdClass();
        $policy->code = $claim->policy_id;
        $policy->name = $claim->document_no;
        $claim->policy = $policy;
        $claim->date_of_loss = Carbon::createFromFormat('d/M/Y', $claim->date_of_loss)->format('Y-m-d');
        $claim->notification_date = Carbon::createFromFormat('d/M/Y', $claim->notification_date)->format('Y-m-d');
        return $claim;
    }

    public function update($id, $data)
    {
        $policy = Policy::findOr($data->policy_id, fn() => throw new Exception("Policy not found"));
        $claim = $this->claimRegister->findOr($id, fn() => throw new Exception("Claim not found"));
        $insuredPerson = DataDetailView::findOr($data->data_detail_id, fn() => throw new Exception("Insured person not found"));
        $claim->update([
            'policy_id' => $policy->id,
            'data_id' => $policy->data_id,
            'data_detail_id' => $data->data_detail_id,
            'insured_person_uuid' => $insuredPerson->insured_person_uuid,
            'cause_of_loss' => $data->cause_of_loss,
            'cause_of_loss_disability' => $data->cause_of_loss_disability,
            'date_of_loss' => $data->date_of_loss,
            'location_of_loss' => $data->location_of_loss,
            'loss_description' => $data->loss_description,
            'reserve_amount' => $data->reserve_amount,
            'clinic_id' => $data->clinic_id,
            'notification_date' => $data->notification_date,
            'schema_plan' => $data->schema_plan,
            'schema_type' => $data->schema_type,
            'schema_detail_code' => $data->schema_detail_code
        ]);
        return $claim;
    }

    public function approve($id, $data)
    {
        $claim = $this->claimRegister::findOr($id, fn() => throw new Exception("Claim registration not found"));
        if (!is_null($claim->approved_status))
            throw new Exception("Claim registration status has already been updated");
        if (($claim->updated_by ?? $claim->created_by) === auth()->id())
            throw new Exception("Maker and approver can not be the same person");
        $binding = [
            $id,
            null,
            $data->status,
            'CLAIM',
            $data->comment,
            auth()->id()
        ];

        $result = DB::select('select * from ins_hs_claim_approve_or_reject(?, ?, ?, ?, ?, ?)', $binding);

        if ($result[0] && $result[0]->response_code == 200) {
            return true;
        }
        info("Failed to approve/reject", ['query' => $result]);
        throw new Exception("Approve/Reject failed");
    }

    public function reviseSchema(ClaimRegisterV $claim, $data)
    {
        $revision = [];
        $bindings = [
            $claim->claim_detail_id,
            $data->comment,
            auth()->id()
        ];
        $query = DB::select('select * from ins_hs_claim_do_revision_schema(?,?,?)', $bindings);
        if (!isset($query[0]) || $query[0]->response_code != 200) {
            throw new Exception(isset($query[0]) ? $query[0]->response_message : 'Schema revision failed');
        }
        return $revision;
    }

    public function delete($id)
    {
        $claim = $this->claimRegister->findOr($id, fn() => throw new Exception("Claim not found"));
        $claim->status = "DEL";
        $claim->update();
        return $claim;
    }
    public function causeOfLoss($policyId, $insuredId): array
    {
        if ($policyId && $insuredId) {
            $statement = DB::select('select * from ins_hs_get_claim_cause_of_loss(?,?)', [$policyId, $insuredId]);
            return collect($statement)->map(function ($item) {
                $item->code = $item->cause_of_loss;
                $item->name = $item->cause_of_loss;
                $item->disabled = !$item->is_claimable;
                return $item;
            })->toArray();
        }
        return [];
    }
    public function schemaData($claim, $print = false)
    {
        if ($claim->is_schema_created) {
            $schemaData = collect(DB::select("select * from ins_hs_get_claim_plan_data(?)", [$claim->claim_id]))
            ->filter(function ($item) use ($print) {
                // If $print is false, only include items with 'actual_incurred_expense' not null
                return $print || !is_null($item->actual_incurred_expense);
            })
            ->values()->map(function ($item) {
                $item->admission_date = Carbon::parse($item->admission_date)->format('Y-m-d');
                $item->discharge_date = Carbon::parse($item->discharge_date)->format('Y-m-d');
                return $item;
            });
        } else {
            $schemaData = DB::select("select *,null AS number_of_day,null AS actual_incurred_expense,null AS schema_id,null AS admission_date, null AS discharge_date from ins_hs_get_claim_schema_data(?)", [$claim->claim_id]);
        }
        return $schemaData;
    }

    private function uniqueSchema($schemaData): array
    {
        $uniqueschemaData = [];
        $seenschemaData = [];

        foreach ($schemaData as $key => $schema) {
            if (!isset($seenschemaData[$schema->schema_name])) {
                $uniqueschemaData[$schema->schema_name] = $schema;
                $seenschemaData[$schema->schema_name] = true;
            } else {
                if (isset($schema->id)) {
                    $uniqueschemaData[$schema->schema_name] = $schema;
                }
            }
        }
        return array_values($uniqueschemaData);
    }

    public function claimHistories($claim)
    {
        return ClaimHistoryV::whereSchemaType($claim->schema_type)
            ->whereCycle($claim->cycle)
            ->whereInsuredPersonUuid($claim->insured_person_uuid)
            ->whereOriginalDocumentNo($claim->original_document_no)
            ->where('claim_detail_id', '<>', $claim->claim_detail_id)
            ->when($claim->schema_tye !== 'STANDARD', function ($q) use ($claim) {
                $q->whereSchemaDetailCode($claim->schema_detail_code);
            })
            ->get();
    }
    private function schemaVersion($claim)
    {
        $result = DB::select('select * from ins_hs_claim_detail_get_next_version(?)', [$claim->claim_id]);
        info("cal schema", ['result' => $result]);
        if (!isset($result[0]) || $result[0]->response_code !== 200 || !$result[0]->version) {
            throw new Exception(isset($result[0]) ? $result[0]->response_message : 'Generate version failed', 500);
        }
        return $result[0]->version;
    }
    public function saveSchema($claim, $data)
    {
        if (!is_null($claim->schema_approved_status))
            throw new Exception("Schema data has already been updated", 403);
        $data = json_decode(json_encode($data), associative: false);
        $claimDetail = $claim->claim_detail_id ? ClaimRegisterDetail::findOr($claim->claim_detail_id, fn() => throw new Exception("Claim detail not found", 500)) : new ClaimRegisterDetail();
        $claimDetail->claim_id = $claim->claim_id;
        $claimDetail->date_of_disability = $data->date_of_disability;
        $claimDetail->date_of_completed_doc = $data->date_of_completed_doc;
        $claimDetail->due_to = $data->due_to;
        $claimDetail->total_actual_incurred_expense = $data->total_actual_incurred_expense;
        $claimDetail->total_maximum_payable = $data->total_maximum_payable;
        $claimDetail->total_non_payable_expense = $data->total_non_payable_expense;
        if (!$claim->claim_detail_id) {
            $claimDetail->version = $this->schemaVersion($claim);
            $claimDetail->save();
        } else {
            $claimDetail->update();
        }
        $schemaSet = collect($data->schema_data)->transform(function($item){
            return (object)$item;
        })->values();
        foreach ($schemaSet as $key => $schemaData) {
            if ($schemaData->actual_incurred_expense) {
                if (isset($schemaData->schema_id)) {
                    $claimSchema = ClaimSchemaData::findOr($schemaData->schema_id, fn() => throw new Exception("Schema detail $schemaData->schema_id not found", 500));
                    $claimSchema->update([
                        'status' => 'ACT',
                        'claim_detail_id' => $claimDetail->id,
                        'schema_detail_code' => $schemaData->schema_detail_code,
                        'admission_date' => @$schemaData->admission_date,
                        'discharge_date' => @$schemaData->discharge_date,
                        'number_of_day' => @$schemaData->number_of_day,
                        'max_number_of_day' => $schemaData->max_number_of_day,
                        'schema_name' => $schemaData->schema_name,
                        'limit_amount' => $schemaData->limit_amount,
                        'actual_incurred_expense' => $schemaData->actual_incurred_expense,
                        'maximum_payable' => $schemaData->maximum_payable,
                        'non_payable_expense' => $schemaData->actual_incurred_expense - $schemaData->maximum_payable
                    ]);
                } else {
                    info("Not isset",['data' => $schemaData]);
                    $claimSchema = ClaimSchemaData::create([
                        'status' => 'ACT',
                        'claim_detail_id' => $claimDetail->id,
                        'schema_detail_code' => $schemaData->schema_detail_code,
                        'admission_date' => @$schemaData->admission_date,
                        'discharge_date' => @$schemaData->discharge_date,
                        'number_of_day' => @$schemaData->number_of_day,
                        'max_number_of_day' => $schemaData->max_number_of_day,
                        'schema_name' => $schemaData->schema_name,
                        'limit_amount' => $schemaData->limit_amount,
                        'actual_incurred_expense' => $schemaData->actual_incurred_expense,
                        'maximum_payable' => $schemaData->maximum_payable,
                        'non_payable_expense' => $schemaData->actual_incurred_expense - $schemaData->maximum_payable
                    ]);
                }
            }
        }
        $bindings = [
            $claimDetail->id,
            auth()->id()
        ];
        $result = DB::select('select * from ins_hs_do_calc_claim_schema_data(?,?)', $bindings);
        info("cal schema", ['result' => $result,'binding' => json_encode($bindings)]);
        if (!isset($result[0]) || $result[0]->response_code !== 200) {
            throw new Exception(isset($result[0]) ? $result[0]->response_message : 'Schema calculation failed', 500);
        }
        return $claimDetail;
    }

    public function approveSchema($claim, $data)
    {
        $approval = [];
        if (!is_null($claim->schema_approved_status))
            throw new Exception("Schema has already been updated", 403);
        if ($claim->schema->created_by === auth()->id())
            throw new Exception("Maker and approver can not be the same person", 500);
        $bindings = [
            $claim->claim_id,
            $claim->claim_detail_id,
            $data->status,
            'SCHEMA',
            $data->comment,
            auth()->id()
        ];
        $result = DB::select('SELECT * FROM ins_hs_claim_approve_or_reject(?,?,?,?,?,?)', $bindings);
        if (!isset($result[0]) || in_array($result[0]->response_code, [400, 500])) {
            Log::error("Approve schema failed", ['result' => $result, 'bindings' => $bindings]);
            throw new Exception(@$result[0]->response_message);
        }
        if ($claim->schema_type !== 'STANDARD') {
            $approval['remaining'] = $this->getClaimRemaining($claim, $data->status === 'APV', true);
        }
        return $approval;
    }

    private function getClaimRemaining($claim, $fnc = true, $latest = false)
    {
        $query = DB::select('select * from ins_hs_get_claim_remaining(?,?,?,?)', [
            $claim->claim_id,
            $fnc ? $claim->claim_detail_id : null,
            null,
            $latest ? 'LATEST' : ''
        ]);
        if (!isset($query[0]) || $query[0]->response_code !== 200) {
            info("cal schema", ['result' => $query]);
            throw new Exception(isset($query[0]) ? $query[0]->response_message : 'OPD remaining amount calculation failed', 500);
        }
        return $query[0]->remaining;
    }
}
