<?php

namespace App\Http\Controllers\ProductConfiguration\ClauseMaintenance;

use App\Http\Controllers\Controller;
use App\Models\ProductLine\ProductLine;
use App\Models\RefEnum\RefEnum;
use Http;

class ClauseMaintenanceServiceController extends Controller
{
    private $serviceUrl;
    const DEFUALT_LANG = 'EN';
    public function __construct()
    {
        $this->serviceUrl = config('pgi.api_insurance_service_url');
    }
    public function getServices()
    {
        $clauseTypes = RefEnum::where('group_id', 'QP_CONFIG')->where('type_id', 'CLAUSE_TYPE')->get()->pluck('name', 'enum_id');
        $productLines = ProductLine::where('status', 'ACT')->get()->pluck('code', 'code');

        return [
            'clauseTypeOptions' => $clauseTypes,
            'productLineOptions' => $productLines
        ];
    }

    public function getClauseTypes()
    {
        $clauseTypes = RefEnum::where('group_id', 'QP_CONFIG')->where('type_id', 'CLAUSE_TYPE')->get()->pluck('name', 'enum_id');

        return $clauseTypes;
    }
    public function clauseType($productLineCode)
    {
        $requestBody = [
            'productLineCode' => $productLineCode
        ];
        $response = Http::withHeaders(['Accept-Language' => self::DEFUALT_LANG])->get($this->serviceUrl . '/clauses/type', $requestBody);
        $response->throw();
        $data = json_decode($response->body())->response;

        $data = collect($data)->map(function ($item) {
            return [
                'label' => $item->name,
                'value' => $item->enumId
            ];
        });
        return response()->json($data);
    }
}
