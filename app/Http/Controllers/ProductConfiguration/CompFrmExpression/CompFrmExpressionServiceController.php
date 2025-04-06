<?php

namespace App\Http\Controllers\ProductConfiguration\CompFrmExpression;

use App\Http\Controllers\Controller;
use App\Models\CompFrmExpression\CompFrmExpression;

class CompFrmExpressionServiceController extends Controller
{
    public function listComponentExpression(){
        $cond_expr = CompFrmExpression::where('cond_type','Y')->groupby('cond_expr')->pluck('cond_expr');
        return response()->json($cond_expr);
    }
}
