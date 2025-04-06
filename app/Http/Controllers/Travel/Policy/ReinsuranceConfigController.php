<?php

namespace App\Http\Controllers\Travel\Policy;

use App\Http\Controllers\Controller;
use App\Models\Travel\Policy\ReinsuranceConfig\ReinsuranceConfig;

class ReinsuranceConfigController extends Controller
{
    CONST ERROR_MESSAGE = "Something went wrong!";
  public function getDefaultReinsuranceConfig($code)
  {
    return ReinsuranceConfig::where('status', 'ACT')
     // ->where('lvl', 1)
      //->where('product_code', $code)
      ->whereDate('start_to', '>=', \Carbon\Carbon::now())
      ->pluck('partner_code');
  }
}
