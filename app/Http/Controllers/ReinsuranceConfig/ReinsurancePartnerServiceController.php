<?php

namespace App\Http\Controllers\ReinsuranceConfig;

use App\Http\Controllers\Controller;
use App\Models\ReinsuranceConfig\ReinsurancePartnerGroup;

class ReinsurancePartnerServiceController extends Controller
{
    public function getReinsuranceGroupCodeList(){
        return ReinsurancePartnerGroup::getGroupCodeList();
    }
}
