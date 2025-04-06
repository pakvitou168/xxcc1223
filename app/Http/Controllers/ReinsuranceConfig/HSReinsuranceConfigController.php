<?php

namespace App\Http\Controllers\ReinsuranceConfig;

use Exception;
use App\Traits\DataTable;
use App\Models\Product\Product;
use App\Http\Controllers\Controller;
use App\Models\HS\ReinsuranceConfig\ReinsuranceConfig;

class HSReinsuranceConfigController extends Controller
{
  public function getDefaultReinsuranceConfig($code)
  {
    return ReinsuranceConfig::where('status', 'ACT')
      ->where('lvl', 1)
      ->where('product_code', $code)
      ->whereDate('start_to', '>=', \Carbon\Carbon::now())
      ->pluck('partner_code');
  }
}
