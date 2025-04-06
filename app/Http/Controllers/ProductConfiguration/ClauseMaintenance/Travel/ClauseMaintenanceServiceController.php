<?php

namespace App\Http\Controllers\ProductConfiguration\ClauseMaintenance\Travel;

use App\Http\Controllers\Controller;
use Http;
use Illuminate\Support\Facades\Log;

class ClauseMaintenanceServiceController extends Controller
{
  private $serviceUrl;
  const DEFUALT_LANG = 'EN';
  const ERROR_MSG = "Something went wrong!";
  public function __construct()
  {
    $this->serviceUrl = config('pgi.api_insurance_service_url');
  }

  public function clauseType($productLineCode)
  {
    try {
      $requestBody = [
        'productLineCode' => $productLineCode
      ];
      $response = Http::withHeaders(['Accept-Language' => self::DEFUALT_LANG])->get($this->serviceUrl . '/clauses/type', $requestBody);
      $response->throw();
      $data = json_decode($response->body())->response;

      $data = collect($data)->map(function ($item) {
        return [
          'label' => $item->name,
          'value' => $item->enum_id,
        ];
      });
      return response()->json($data);

    }catch(\Exception $e) {
      Log::error('Travel clause type - failed to get clause type from API : ' . $e->getMessage());

      return response([
        'data' => [],
        'success' => false,
        'message' => $this::ERROR_MSG
      ], 500);
    }
  }
}