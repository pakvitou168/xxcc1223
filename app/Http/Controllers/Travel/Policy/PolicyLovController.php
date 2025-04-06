<?php

namespace App\Http\Controllers\Travel\Policy;

use App\Http\Controllers\Controller;
use App\Models\RefEnum;
use Illuminate\Http\Request;

class PolicyLovController extends Controller
{
    CONST ERROR_MESSAGE = "Something went wrong!";
    /**
     * Get list of values for policy-related dropdowns.
     *
     * @param  Request  $request
     * @return array
     */
    public function getLov(Request $request): array
    {
        return [
            'list_business_types' => $this->listBusinessTypes(),
            'list_policy_types' => $this->listPolicyTypes(),
            'claim_enquiry_benefit' => RefEnum::listClaimEnquiryBenefit(),
        ];
    }

    public function listBusinessTypes()
    {
        return RefEnum::select('enum_id', 'name')
            ->where('group_id', 'POLICY_CONFIG')
            ->where('type_id', 'BUSINESS_TYPE')
            ->pluck('name', 'enum_id');
    }

    public function listPolicyTypes()
    {
        return RefEnum::select('enum_id', 'name')
            ->where('group_id', 'POLICY_CONFIG')
            ->where('type_id', 'POLICY_TYPE')
            ->pluck('name', 'enum_id');
    }
}