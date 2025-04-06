<?php

namespace App\Http\Controllers\ProductConfiguration\ComponentFormulaElement;

use App\Http\Controllers\Controller;
use App\Models\Formula\Formula;
use App\Models\RefEnum\RefEnum;

class CompFrmElementServiceController extends Controller
{
    public function listFormula(){
        $formula = Formula::where('status', 'ACT')->get()->pluck('formula_code','formula_code');
        return response()->json($formula);
    }

    public function listElementTypes() {
        return RefEnum::where('group_id', 'PROD_CONFIG')->where('type_id', 'ELEM_TYPE')->get()->pluck('name', 'enum_id');
    }
}
