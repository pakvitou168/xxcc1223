<?php

namespace App\Http\Controllers\ReinsuranceConfig;

use Exception;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\ReinsuranceConfig\Reinsurance;
use Illuminate\Validation\ValidationException;
use App\Models\ReinsuranceConfig\ReinsuranceType;
use App\Models\ReinsuranceConfig\ReinsuranceConfig;
use App\Models\ReinsuranceConfig\ReinsurancePartner;

class ReinsuranceConfigController extends Controller
{
  use DataTable;

  public function __construct()
  {
    $this->authorizeResource(ReinsuranceConfig::class, 'reinsurance_config');
  }

  public function index()
  {
    return $this->generateTableData(
      ReinsuranceConfig::with([
        '_product' => function($query) {$query->select('code', 'name');},
        '_reinsurance' => function($query) {$query->select('code', 'name');},
        '_reinsuranceType' => function($query) {$query->select('code', 'name');},
        '_reinsurancePartner' => function($query) {$query->select('code', 'name');},
        '_parentCode' => function($query) {$query->select('code', 'name');},
      ])
      ->where('status', 'ACT')->orderByDesc('id'));
  }

  public function create() {
    //
  }

  public function store(Request $request)
  {
    try {
      $this->validateRequest($request);
      $reinsurance = new ReinsuranceConfig();
      $this->assignValues($request, $reinsurance);

      if (!$this->isValidDates($reinsurance)) {
        return response([
          'success' => false,
          'message' => 'Start From Date cannot be after To Date'
        ], 400);
      }

      if ($this->isOverlapped($reinsurance)) {
        return response([
          'success' => false,
          'message' => 'The date range overlapped existing dates'
        ], 400);
      }

      if ($this->isOverShareLimit($reinsurance)) {
        return response([
          'success' => false,
          'message' => 'Share is over limit'
        ], 400);
      }
      if ($reinsurance->save())
        return [
          'success' => true,
          'message' => "Reinsurance Config is successfully created!"
        ];
    } catch (\Throwable $th) {
      if($th instanceof ValidationException)
        return [
          'error' => $th->errors(),
          'message' => $th->getMessage()
        ];
      else
        return [
          'error' => true,
          'message' => $th->getMessage()
        ];
    }
  }

  public function show(ReinsuranceConfig $reinsuranceConfig) {
    if($reinsuranceConfig->status != 'DEL') {
      $reinsuranceConfig->product_code = Product::select('code', 'name')
        ->where('code', $reinsuranceConfig->product_code)
        ->first() ?? $reinsuranceConfig->product_code;

      $reinsuranceConfig->reinsurance_code = Reinsurance::select('code', 'name')
        ->where('code', $reinsuranceConfig->reinsurance_code)
        ->first() ?? $reinsuranceConfig->reinsurance_code;

      $reinsuranceConfig->reinsurance_type = ReinsuranceType::select('code', 'name')
        ->where('code', $reinsuranceConfig->reinsurance_type)
        ->first() ?? $reinsuranceConfig->reinsurance_type;

      $reinsuranceConfig->partner_code = ReinsurancePartner::select('code', 'name')
        ->where('code', $reinsuranceConfig->partner_code)
        ->first() ?? $reinsuranceConfig->partner_code;

      $reinsuranceConfig->parent_code = ReinsurancePartner::select('code', 'name')
        ->where('code', $reinsuranceConfig->parent_code)
        ->first() ?? $reinsuranceConfig->parent_code;
      return $reinsuranceConfig;
    }
    else
        return [
            'error' => true,
            'message' => "Sorry, can't find a record."
        ];
  }

  public function edit(ReinsuranceConfig $reinsuranceConfig) {
    if($reinsuranceConfig->status != 'DEL') {
      return $reinsuranceConfig;
    }
    else
      return [
        'error' => true,
        'message' => "Sorry, can't find a record."
      ];
  }

  public function update(Request $request, ReinsuranceConfig $reinsuranceConfig)
  {
    try {
      $this->validateRequest($request);
      $this->assignValues($request, $reinsuranceConfig);

      if (!$this->isValidDates($reinsuranceConfig)) {
        return response([
          'success' => false,
          'message' => 'Start From Date cannot be after To Date'
        ], 400);
      }

      if ($this->isOverlapped($reinsuranceConfig)) {
        return response([
          'success' => false,
          'message' => 'The date range overlapped existing dates'
        ], 400);
      }

      if ($this->isOverShareLimit($reinsuranceConfig)) {
        return response([
          'success' => false,
          'message' => 'Share is over limit'
        ], 400);
      }
      if ($reinsuranceConfig->update()) {
        return [
          'success' => true,
          'message' => 'Record is updated.'
        ];
      }
    } catch (\Throwable $th) {
      if ($th instanceof ValidationException)
        return [
          'error' => $th->errors(),
          'message' => $th->getMessage()
        ];
      else
        return [
          'error' => true,
          'message' => $th->getMessage()
        ];
    }
  }

  public function destroy(ReinsuranceConfig $reinsuranceConfig)
  {
    try {
      if ($reinsuranceConfig->status === 'DEL') throw new Exception('Record not found!');
      $reinsuranceConfig->status = 'DEL';
      if ($reinsuranceConfig->save()) {
        return [
          'success' => true,
          'message' => 'Record is deleted.'
        ];
      }
    } catch (\Throwable $th) {
      Log::error('Reinsurance delete error : ' . $th);
      return [
        'error' => true,
        'message' => $th->getMessage()
      ];
    }
  }

  public function productsByProductLine($productLineCode)
  {
    return Product::listProductsByProductLine($productLineCode);
  }

  public function parentByProductCode($code)
  {
    return ReinsuranceConfig::where('status', 'ACT')
      ->where('lvl', 1)
      ->where('product_code', $code)
      ->get()
      ->map(function ($item) {
        return [
          'value' => $item->partner_code,
          'label' => $item->partner_code
        ];
      })
      ->values()
      ->toArray();
  }

  public function getDefaultReinsuranceConfig($code)
  {
    return ReinsuranceConfig::where('status', 'ACT')
      ->where('lvl', 1)
      ->where('product_code', $code)
      ->whereDate('start_to', '>=', \Carbon\Carbon::now())
      ->get()
      ->map(function ($item) {
        return [
          'value' => $item->partner_code,
          'label' => $item->partner_code
        ];
      })
      ->values()
      ->toArray();
  }

  public function _3re()
  {
    $reinsurance = Reinsurance::select('code', 'name')
      ->where('status', '<>', 'DEL')
      ->get()->map(function ($item) {
        $item->label = $item->name;
        $item->value = $item->code;
        return collect($item)->only(['value', 'label']);
      });
    $type = ReinsuranceType::select('code', 'name')
      ->where('status', '<>', 'DEL')
      ->get()->map(function ($item) {
        $item->label = $item->name;
        $item->value = $item->code;
        return collect($item)->only(['value', 'label']);
      });
    $partner = ReinsurancePartner::select('code', 'name')
      ->where('status', '<>', 'DEL')
      ->get()->map(function ($item) {
        $item->label = $item->name;
        $item->value = $item->code;
        return collect($item)->only(['value', 'label']);
      });
    return [
      'reinsurance' => $reinsurance,
      'type' => $type,
      'partner' => $partner,
    ];
  }

  private function assignValues(Request $request, ReinsuranceConfig $reinsuranceConfig)
  {
    $reinsuranceConfig->product_line_code = $request->product_line_code;
    $reinsuranceConfig->product_code = $request->product_code;
    $reinsuranceConfig->reinsurance_type = $request->reinsurance_type;
    $reinsuranceConfig->reinsurance_code = $request->reinsurance_code;
    $reinsuranceConfig->partner_code = $request->partner_code;
    $reinsuranceConfig->start_from = $request->start_from;
    $reinsuranceConfig->start_to = $request->start_to;
    $reinsuranceConfig->leaf = $request->leaf;
    if ($request->parent_code) $reinsuranceConfig->lvl =  2;
    else $reinsuranceConfig->lvl = 1;
    $reinsuranceConfig->parent_code = $request->parent_code;
    $reinsuranceConfig->share_basis = $request->share_basis;
    $reinsuranceConfig->uw_year = $request->uw_year;
    $reinsuranceConfig->share = $request->share;
    $reinsuranceConfig->amount_cap = $request->amount_cap;
    $reinsuranceConfig->ri_commission = $request->ri_commission;
    $reinsuranceConfig->tax_fee = $request->tax_fee;
  }

  private function isValidDates($reinsuranceConfig) {
    return $reinsuranceConfig->start_from <= $reinsuranceConfig->start_to;
  }

  private function isOverlapped($reinsuranceConfig) {
    return ReinsuranceConfig::where('product_code', $reinsuranceConfig->product_code)
      ->where('lvl', $reinsuranceConfig->lvl)
      ->where('id', '<>', $reinsuranceConfig->id)
      ->where('partner_code', $reinsuranceConfig->partner_code)
      ->whereDate('start_from', '<=', $reinsuranceConfig->start_to)
      ->whereDate('start_to', '>=', $reinsuranceConfig->start_from)
      ->when($reinsuranceConfig->lvl === 2, function($query) use ($reinsuranceConfig) {
        $query->where('parent_code', $reinsuranceConfig->parent_code);
      })
      ->where('status', 'ACT')
      ->exists();
  }

  private function isOverShareLimit($reinsuranceConfig)
  {
    $existingShare = $this->sumShare($reinsuranceConfig);
    // Sum between existing and current share
    if ($existingShare + $reinsuranceConfig->share > 100) return true;

    return false;
  }

  private function sumShare($reinsuranceConfig)
  {
    $shares = ReinsuranceConfig::where('product_code', $reinsuranceConfig->product_code)
      ->where('lvl', $reinsuranceConfig->lvl)
      ->where('id', '<>', $reinsuranceConfig->id)
      ->where('uw_year', $reinsuranceConfig->uw_year)
      ->when($reinsuranceConfig->lvl === 2, function ($query) use ($reinsuranceConfig) {
        $query->where('parent_code', $reinsuranceConfig->parent_code);
      })
      ->where('status', 'ACT')
      ->get()
      ->map(function ($item) {
        return [
          'value' => $item->share,
          'label' => $item->share
        ];
      })
      ->pluck('value')
      ->sum();

    return $shares;
  }

  private function validateRequest(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'product_line_code' => ['required'],
      'product_code' => ['required'],
      'reinsurance_type' => ['required'],
      'reinsurance_code' => ['required'],
      'partner_code' => ['required'],
      'start_from' => ['required'],
      'start_to' => ['required'],
      'leaf' => ['required'],
      // 'parent_code' => ['required'],
      // 'share_basis' => ['required'],
      'uw_year' => ['required'],
      'share' => ['required'],
      'amount_cap' => ['required'],
      'ri_commission' => ['required'],
      'tax_fee' => ['required'],
    ]);

    if ($validator->fails()) {
      Log::error('ReinsuranceConfigController - Incorrect validate data input : ' . $validator->errors());
      throw new ValidationException($validator, 'Validate data input was incorrectly!');
    }
  }
}
