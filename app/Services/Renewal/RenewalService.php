<?php

namespace App\Services\Renewal;

use App\Models\Insurance\Policy;
use App\Models\Renewal\Renewal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RenewalService {
  public function generateRenewalList(int $underWritingYear, string $expiredFromDate, string $expiredToDate): bool {
    $params = [
      $underWritingYear,
      $expiredFromDate,
      $expiredToDate,
      auth()->id(),
    ];

    info('generateRenewalList: ', [
      'underWritingYear' => $underWritingYear,
      'expiredFromDate' => $expiredFromDate,
      'expiredToDate' => $expiredToDate]
    );
    $generated = DB::select("select * from ins_generate_renewal_list(?,?,?,?)", $params);
    if (optional($generated[0])->code === 'SUC') {
      info('Success. Result : ', $generated);

      return true;
    }
    Log::error('Failed. Result : ', $generated);
  
    return false;
  }

  /**
   * List all renewals that have not generated new data yet
   */
  public function listNoDetailRenewals() {
    return Renewal::whereNull('data_id')
      ->where('submit_status', Renewal::DRAFT)
      ->where('status', Renewal::PENDING)
      ->where('accept_status', Renewal::PENDING)
      ->get();
  }

  public function generateRenewalDetail($renewalId) {
    $params = [
      $renewalId,
      auth()->id(),
    ];

    info('generateRenewalDetail: ', ['renewalId' => $renewalId]);

    $generated = DB::select("select * from ins_prod_auto_generate_renew_policy_detail(?,?)", $params);

    if (optional($generated[0])->code === 'SUC') {
      info('Success. Result : ', $generated);

      return true;
    }
    Log::error('Failed. Result : ', $generated);
  
    return false;
  }

  public function autoApproveNoClaimPolicies() {
    $params = [
      'auto approved',
      auth()->id(),
    ];

    info('autoApproveNoClaimPolicies');

    $generated = DB::select("select * from ins_renewal_no_claim_policy_auto_approval(?,?)", $params);

    if (optional($generated[0])->code === 'SUC') {
      info('Success. Result : ', $generated);

      return true;
    }
    Log::error('Failed. Result : ', $generated);
  
    return false;
  }

  public function generateRenewedPolicy($renewalId, $remark = ''): bool {
    $params = [
      $renewalId,
      auth()->id(),
      $remark,
    ];

    info('generateRenewedPolicy: ', [
      'renewalId' => $renewalId,
    ]);

    $generated = DB::select("select * from ins_renewal_generate_policy(?,?,?)", $params);

    if (optional($generated[0])->code === 'SUC') {
      info('Success. Result : ', $generated);

      return true;
    }
    Log::error('Failed. Result : ', $generated);
  
    return false;
  }

  public function canGenerateRenewedPolicy($renewalId): bool {
    info('canGenerateRenewedPolicy: ', [
      'renewalId' => $renewalId,
    ]);

    $renewal = Renewal::findOr($renewalId, fn() => abort(404, 'Not found.'));

    $isApproved = $renewal->submit_status === Renewal::APPROVED && $renewal->accept_status === Renewal::APPROVED;
    if (!$isApproved) return false;

    $latestCycle = Renewal::where('policy_no', $renewal->policy_no)->max('cycle');

    if ($renewal->cycle !== $latestCycle) return false;
  
    $hasActiveGeneratedPolicy = Policy::where('policy_no', $renewal->policy_no)
      ->where('cycle', $renewal->cycle)
      ->exists();

    if ($hasActiveGeneratedPolicy) return false;

    return true;
  }

  public function update(array $data, int $renewalId): bool {
    info('update: ', [
      'data' => $data,
      'renewalId' => $renewalId,
    ]);

    return Renewal::findOr($renewalId, fn() => abort(404, 'Not found.'))
      ->update($data); 
  }

  public function canBeApproved($renewalId): bool {
    info('canBeApproved: ', [
      'renewalId' => $renewalId,
    ]);

    $renewal = Renewal::findOr($renewalId, fn() => abort(404, 'Not found.'));

    return $renewal->submit_status === Renewal::PENDING && $renewal->status === Renewal::PENDING;
  }

  public function canBeAccepted($renewalId): bool {
    info('canBeAccepted: ', [
      'renewalId' => $renewalId,
    ]);

    $renewal = Renewal::findOr($renewalId, fn() => abort(404, 'Not found.'));

    return $renewal->submit_status === Renewal::APPROVED && $renewal->status === Renewal::PENDING;
  }

  public function canGenerateRenewedPolicyNewVersion($renewalId): bool {
    info('canGenerateRenewedPolicyNewVersion: ', [
      'renewalId' => $renewalId,
    ]);

    $renewal = Renewal::findOr($renewalId, fn() => abort(404, 'Not found.'));
    return $renewal->submit_status === Renewal::APPROVED
      && $renewal->accept_status === Renewal::REJECTED
      && $renewal->status === Renewal::LOSS;
  }

  public function generateNewReNewVersion($renewalId): bool {
    $params = [
      $renewalId,
      auth()->id(),
    ];

    info('generateNewReNewVersion: ', [
      'renewalId' => $renewalId,
    ]);

    $generated = DB::select("select * from ins_prod_auto_gen_new_renew_policy_version(?,?)", $params);
    if (optional($generated[0])->code === 'SUC') {
      info('Success. Result : ', $generated);

      return true;
    }
    
    throw new \Exception($generated[0]->message ?? 'Failed to generate new version');
    Log::error('Failed. Result : ', $generated);
  
    return false;
  }

  public function canBeRevised($renewalId): bool {
    info('canBeRevised: ', [
      'renewalId' => $renewalId,
    ]);

    $renewal = Renewal::findOr($renewalId, fn() => abort(404, 'Not found.'));

    return $renewal->submit_status === Renewal::REJECTED;
  }
}