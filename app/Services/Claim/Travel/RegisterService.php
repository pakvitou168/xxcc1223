<?php

namespace App\Services\Claim\Travel;

use App\Models\Travel\Claim\ClaimablePolicyV;
use App\Models\Travel\Claim\ClaimRegister;
use App\Models\Travel\Claim\ClaimRegisterV;
use App\Models\Travel\DataDetailV;
use App\Models\Travel\Policy\DataDetailView;
use App\Models\Travel\Policy\Policy;
use Exception;
use Illuminate\Support\Facades\Http;

class RegisterService
{
    private $serviceUrl;

    public function __construct(private ClaimRegister $claimRegister, private ClaimRegisterV $claimRegisterV)
    {
        $this->serviceUrl = config('pgi.api_insurance_service_url');
    }

    public function policyList($search = null)
    {
        return ClaimablePolicyV::when($search, function ($q) use ($search) {
            $q->where('document_no', 'ILIKE', $search . "%")->orWhere('policy_no', 'ILIKE', $search . "%");
        })->limit(100)->select('document_no AS name', 'policy_id AS code','data_id')->get()->values();
    }

    public function detail($id)
    {
        $claim = $this->claimRegisterV->findOr($id, fn() => throw new Exception("Claim not found"));
        $claim->insuredPerson->age = ceil($claim->insuredPerson->age);
        // $claim->load('reinsurances');
        return $claim;
    }

    public function insuredPersonsByPolicy()
    {
        $search = request()->search;
        $policy = Policy::findOr(request()->policy_id, fn() => throw new Exception("Incorrect policy number"));
        return DataDetailV::whereDataId($policy->data_id)
            // ->whereLangCode('EN')
            ->when($search, function ($q) use ($search) {
                $q->where('full_name', 'ILIKE', $search . "%");
            })
            // ->where(function ($q) {
            //     $q->whereNull('endorsement_state')->orWhere('endorsement_state', '<>', 'DELETION');
            // })
            ->select('full_name as name', 'no as code', 'gender', 'date_of_birth as dob')->limit(100)->get();
    }

    public function causeOfLoss($policyId): array
    {
        $response = Http::get($this->serviceUrl . '/tv/claims/cause',['dataId' => $policyId]);
        $response->throw();
        $data = json_decode($response->body());
        if (!$data || $data->code !== 'SUC-000') {
            Log::error('causeOfLoss: ', ['result' => json_encode($data)]);
            throw new Exception($data->message ?? "get cause of loss data failed", 500);
        }
        return $data->response;
    }
}
