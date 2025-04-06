<?php

namespace App\Services\Travel\Policy;

use App\Models\Address\AddressCode;
use App\Models\CustomerManagement\Country;
use App\Models\CustomerManagement\Customer;
use App\Models\CustomerManagement\CustomerClassification;
use App\Models\Travel\Policy\DataDetail;
use App\Models\Travel\Policy\DataMaster;
use App\Models\Travel\Policy\Insurance\EndorsementView;
use App\Models\Travel\Policy\InsuredPersonView;
use App\Models\Travel\Policy\Policy;
use App\Models\Travel\Policy\QuotationDetailDataView;
use App\Models\Travel\Policy\SchemaData;
use App\Models\Insurance\InsuranceClause;
use App\Models\Product\Product;
use App\Models\RefEnum;
use App\Models\UserManagement\User\UserFile;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use KhmerDateTime\KhmerDateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PolicyEndorsementService
{
  const END_STATE_ADD = 'ADDITION';
  const END_STATE_DEL = 'DELETION';
  const END_STATE_AMD = 'AMENDMENT';
  public function listEndorsementTypes()
  {
    return RefEnum::where('type_id', 'tv_ENDORSEMENT_TYPE')
      ->where('group_id', 'POLICY_CONFIG')
      ->pluck('name', 'enum_id');
  }

  public function getEffectivePeriod($id)
  {
    $masterId = Policy::where('id', $id)->value('data_id');
    return DataMaster::select('effective_date_from as from', 'effective_date_to as to')
      ->where('id', $masterId)
      ->first();
  }

  public function getEndorsementType($id)
  {
    $masterId = Policy::where('id', $id)->value('data_id');

    return DataMaster::where('id', $masterId)->value('endorsement_type');
  }

  public function isLatestEndorsement($id)
  {
    $currentPolicy = Policy::where('id', $id)->first();
    $endorsementCount = Policy::where('policy_no', $currentPolicy->policy_no)
      ->where('cycle', $currentPolicy->cycle)
      ->whereNotNull('version')
      ->count();
    if ($endorsementCount == $currentPolicy->version)
      return true;
    return false;
  }

  public function isApproved($id)
  {
    return Policy::where('status', Policy::APPROVED)->where('id', $id)->exists();
  }

  public function generate($policyId, $endorsementType, $effectiveDate, $desc, $userId): bool
  {
    $params = [
      $policyId,
      $endorsementType,
      $effectiveDate,
      $desc,
      $userId
    ];

    info('TravelEndorsementService: generate', ['params' => $params]);
    if ($endorsementType == 'ADD/DELETE') {
      $response = DB::select('select * from ins_tv_prod_gen_endorsement_add_delete(?,?,?,?,?)', $params);
    } else {
      $response = DB::select('select * from ins_tv_prod_gen_new_policy_endorsement(?,?,?,?,?)', $params);
    }

    info('TravelEndorsementService: generate', ['res' => $response]);

    if (optional($response[0])->code) {
      return true;
    }
    return false;
  }

  public function getDataDetail($id)
  {
    $dataMaster = DataMaster::with('product')->withCount('hsDetails')->findOr($id, fn() => abort(404, 'Not found'));
    $endorsement = EndorsementView::where('data_id', $id)->first();
    $policy_no = EndorsementView::where('data_id', $id)->value('document_no');

    $customer = Customer::where('customer_no', $dataMaster->customer_no)->first();


    if (!$policy_no)
      abort(404, 'Not found');

    $effectiveDateFrom = App::isLocale('en') ? Carbon::parse($dataMaster?->effective_date_from)->format('d/F/Y') : KhmerDateTime::parse($dataMaster?->effective_date_from)->format('LL');
    $effectiveDateTo = App::isLocale('en') ? Carbon::parse($dataMaster?->effective_date_to)->format('d/F/Y') : KhmerDateTime::parse($dataMaster?->effective_date_to)->format('LL');

    $endorsement->issued_on = $endorsement->updated_at ?? $endorsement->created_at ?? null;

    if ($endorsement->updated_by) {
      $endorsement->issued_by = $endorsement->issuedByName($endorsement->updated_by);
    } elseif ($endorsement->created_by) {
      $endorsement->issued_by = $endorsement->issuedByName($endorsement->created_by);
    } else {
      $endorsement->issued_by = null;
    }

    return [
      'issued_on' => App::getLocale() == 'en' ? date('d F Y', strtotime($endorsement->issued_at)) : KhmerDateTime::parse($endorsement->issued_at)->format('LL'),
      'issued_by' => $endorsement->issued_by,
      'policy_no' => $policy_no,
      'document_no' => $endorsement->document_no,
      'endorsement_type' => $endorsement->endorsement_type,
      'data_id' => $endorsement->data_id,
      'product_code' => $endorsement->product_code,
      'business_code' => $dataMaster?->business_code,
      'insured_name' => $dataMaster?->insured_name,
      'insured_name_kh' => $dataMaster?->insured_name_kh,
      'correspondence_address' => $customer->info()->address,
      'period_of_insurance' => $dataMaster->effective_day . " " . __('Days') . ' - ' . __('From') . ' ' . $effectiveDateFrom . ' ' . __('To') . ' ' . "$effectiveDateTo (" . __('Both Days Inclusive') . ')', //todo
      ...$this->getSignatureByEndorsement($endorsement),
      'endorsement_premium' => $this->formatPremium($endorsement->total_premium),
      'endorsement_e_date' => $endorsement->endorsement_e_date,
      'total_insured' => $dataMaster->tv_details_count
    ];
  }
  public function getSignatureByEndorsement(EndorsementView $endorsement)
  {
    return [
      'signature' => UserFile::select('file_url')->where('user_id', $endorsement->approved_by)->where('file_type', 'SIGNATURE')->first()
    ];
  }

  public function getDetail($id)
  {
    $endorsement = EndorsementView::with('hs')->findOr($id, fn() => throw new Exception("Endorsement not found"));
    $hs = $endorsement->hs;
    $hs->endorsement_clauses = $this->getInsuranceClauseIds($hs, 'ENDORSEMENT');
    $hs->general_exclusions = $this->getInsuranceClauseIds($hs, 'EXCLUSION');
    $hs->geographical_limit = $hs->insuranceClauses()->where('clause_type', 'GEOGRAPHICAL')->first()?->id;
    unset($endorsement->hs);
    return ['hs' => $hs, 'endorsement' => $endorsement];
  }

  public function getInsuranceClauseIds($policy, $clauseType)
  {
    return $policy->insuranceClauses()->where('clause_type', $clauseType)->get()->pluck('pivot.clause_id');
  }

  public function update($id, $data)
  {
    $endorsement = EndorsementView::findOr($id, fn() => throw new Exception("Endorsement not found"));
    $hs = $endorsement->hs;
    $hs->update([
      'insured_name' => $data->insured_name,
      'insured_name_kh' => $data->insured_name_kh,
      'warranty' => $data->warranty,
      'warranty_kh' => $data->warranty_kh,
      'memorandum' => $data->memorandum,
      'memorandum_kh' => $data->memorandum_kh,
      'subjectivity' => $data->subjectivity,
      'subjectivity_kh' => $data->subjectivity_kh,
      'remark' => $data->remark,
      'remark_kh' => $data->remark_kh,
      'handler_code' => $data->handler_code,
      'business_code' => $data->business_code,
      'sale_channel' => $data->sale_channel,
      'commission_rate' => $data->commission_rate
    ]);

    $hs->insuranceClauses()->syncWithPivotValues([
      $data->geographical_limit,
      ...$data->endorsement_clauses,
      ...$data->general_exclusions
    ], ['status' => 'ACT']);

    return $endorsement;
  }

  public function updateConfig(Policy $policy, $data)
  {
    $policy->update([
      'business_type' => $data->business_type,
      'policy_type' => $data->policy_type
    ]);
  }

  private function manipulateMasterData($data, Policy $policy)
  {
    $data = isset($data[0]) && $policy ? $data[0] : throw new Exception("Master data not found");
  }

  private function manipulatePlanData($data, Policy $policy)
  {
    return count($data) > 0 && $policy;
  }

  private function manipulateSchemaData($data, Policy $policy)
  {
    foreach ($data as $row) {
      SchemaData::updateOrCreate(
        [
          'master_data_id' => $policy->data_id,
          'key' => $row['key'],
          'schema_type' => $row['schema_type'],
          'status' => 'ACT'
        ],
        [
          'age_band' => $row['age_band'],
          'no_female' => $row['no_female'],
          'no_person' => $row['no_person'],
          'rate' => $row['rate'],
          'plan_1' => $row['plan_1'],
          'plan_2' => $row['plan_2'],
          'plan_3' => $row['plan_3'],
          'plan_4' => $row['plan_4'],
          'plan_5' => $row['plan_5'],
          'master_data_type' => $row['master_data_type'],
          'schema_detail_code' => $row['schema_detail_code']
        ]
      );
    }
  }

  private function excelToDate($date)
  {
    return $date ? Date::excelToDateTimeObject($date)->format('Y-m-d') : null;
  }

  private function manipulateInsuredPerson($data, Policy $policy)
  {
    foreach ($data as $row) {
      $dob = $this->excelToDate($row['date_of_birth']);
      $endEffDate = $this->excelToDate($row['endorsement_effective_date']);

      if ($row['insured_id']) {
        $insuredPerson = DataDetail::findOr($row['insured_id'], fn() => throw new Exception('Insured person with ID ' . $row['insured_id'] . ' not found'));
        if ($insuredPerson->master_data_id !== $policy->data_id) {
          Log::error("Policy does not match with insured person", ['data' => $insuredPerson]);
          throw new Exception("Policy does not match with insured person");
        } elseif (strtoupper($row['transaction_type']) === self::END_STATE_DEL) {
          $insuredPerson->endorsement_state = strtoupper($row['transaction_type']);
          $insuredPerson->endorsement_stage = $policy->document_no;
          $insuredPerson->endorsement_e_date = $endEffDate;
          $insuredPerson->premium = $row['endorsement_premium'];
          $insuredPerson->endos_day_remaining = $row['days_of_endorsement'];
          $insuredPerson->update();
        }
      } elseif (strtoupper($row['transaction_type']) === self::END_STATE_ADD) {
        DataDetail::create([
          'master_data_id' => $policy->data_id,
          'date_of_birth' => $dob,
          'gender' => in_array(strtolower($row['gender']), ['m', 'male']) ? 'M' : 'F',
          'name' => $row['name'],
          'occupation' => $row['occupation'],
          'standard_plan' => $row['standard_plan'],
          'optional_plan' => $row['optional_plan'],
          'endorsement_e_date' => $endEffDate,
          'endos_day_remaining' => $row['days_of_endorsement'],
          'premium' => $row['endorsement_premium'],
          'endorsement_stage' => $policy->document_no,
          'endorsement_state' => strtoupper($row['transaction_type']),
          'remark' => $row['endorsement_remark']
        ]);
      }
    }
  }
  public function manipulateInsuredInfoEndorsement(Policy $policy, $sheets)
  {
    $rows = $sheets[0];
    foreach ($rows as $row) {

      $insuredPerson = DataDetail::whereId($row['insured_id'])->whereMasterDataId($policy->data_id)->firstOr(fn() => throw new Exception("Insured id " . $row['insured_id'] . ' could not be found in policy number ' . $policy->document_no));
      
      $isChanged = $this->insuredHasChanged($insuredPerson, $row);
      if ($isChanged) {
        $gender = strtoupper($row['gender']);
        $insuredPerson->date_of_birth = Date::excelToDateTimeObject($row['date_of_birth'])->format('Y-m-d') ?? throw new Exception("Invalid date of birth");
        $insuredPerson->name = $row['name'];
        $insuredPerson->occupation = $row['occupation'];
        $insuredPerson->gender = in_array($gender, ['MALE', 'M']) ? 'M' : (in_array($gender, ['FEMALE', 'F']) ? 'F' : throw new Exception("Incorrect gender value for insured person id: " . $row['insured_id']));
        $insuredPerson->endorsement_stage = $policy->document_no;
        $insuredPerson->endorsement_state = self::END_STATE_AMD;
        $insuredPerson->update();
      }

    }
    $policy->approved_status = 'SBM';
    $policy->update();
  }
  private function insuredHasChanged($insuredPerson, $update)
  {
    // if (Date::excelToDateTimeObject($row['date_of_birth'])->format('Y') !== $insuredPerson->date_of_birth->format('Y')) {
    //   throw new Exception(" Year of birth of insured person with id " . $row['insured_id'] . ' can not be updated due to insured person policies');
    // }
    $origin = $insuredPerson->only(['name', 'gender', 'occupation', 'date_of_birth']);
    $origin['date_of_birth'] = Carbon::parse($origin['date_of_birth'])->format('Y-m-d');

    $change = collect([$update])->transform(function ($item) {
      $item['date_of_birth'] = Date::excelToDateTimeObject($item['date_of_birth'])->format('Y-m-d');
      return collect($item)->only(['name', 'gender', 'occupation', 'date_of_birth'])->all();
    })->first();
    ksort($origin);
    ksort($change);
    return $origin !== $change;
  }

  public function manipulateEndorsement(Policy $policy, $data)
  {
    [, , , $insuredPerson, $masterData, $planDataDetail, $schemaData] = array_values($data); //read out first before process

    $this->manipulateMasterData($masterData, $policy);

    $this->manipulatePlanData($planDataDetail, $policy);

    $this->manipulateSchemaData($schemaData, $policy);

    $this->manipulateInsuredPerson($insuredPerson, $policy);

    if (!$this->finalizeEndorsementAddDel($policy)) {
      throw new Exception("Finalize endorsement errors");
    }
  }

  private function finalizeEndorsementAddDel(Policy $policy): bool
  {
    $params = [
      $policy->id,
      $policy->dataMaster->data_type,
      $policy->data_id,
      auth()->id()
    ];
    info("params endorsement add/delete", ['params' => implode(',', $params)]);
    $response = DB::select('select * from ins_tv_do_endor_gen_insure_person_add_delete(?,?,?,?)', $params);
    info('TravelEndorsementService: finalize add/delete', ['res' => $response]);

    if (optional($response[0])->code) {
      $policy->approved_status = 'SBM';
      $policy->update();
      return true;
    }
    return false;
  }

  public function formatPremium($endorsementPremium): string
  {
    if ($endorsementPremium < 0) {
      return '(' . number_format(abs($endorsementPremium), 2, '.', ',') . ')';
    }

    return number_format($endorsementPremium, 2, '.', ',');
  }
}
