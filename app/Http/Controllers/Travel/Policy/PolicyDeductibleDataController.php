<?php

namespace App\Http\Controllers\Travel\Policy;

use App\Http\Controllers\Controller;
use App\Models\Travel\Policy\DeductibleData;
use Illuminate\Http\JsonResponse;

class PolicyDeductibleDataController extends Controller
{
    CONST ERROR_MESSAGE = "Something went wrong!";
    public function getDeductibleData($data_id): JsonResponse
    {
        $deductible_data = DeductibleData::where('data_id', $data_id)->first();

        return response()->json([
            'deductible_data' => $deductible_data,
        ]);

    }
}