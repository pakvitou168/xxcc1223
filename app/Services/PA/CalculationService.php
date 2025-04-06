<?php

namespace App\Services\PA;

use App\Exceptions\InsException;
use DB;

class CalculationService
{

    public function generatePremium($bindings)
    {
        $query = DB::select('select * from ins_pa_calc_insured_person_premium(?,?)', $bindings);
        $result = collect($query)->first();
        info("QUERY. GENERATE PREMIUM:", ['bindings' => json_encode($bindings), 'result' => json_encode($result)]);
        if ($result && $result->response_code === 200) {
            return $result;
        }
        throw new InsException("Generate premium failed", 500);
    }
    public function generateInvoice($bindings)
    {
        $query = DB::select('select * from ins_generate_policy_invoice_note(?,?,?)', $bindings);
        $result = collect($query)->first();
        info("QUERY. GENERATE INVOICE/CREDIT:", ['bindings' => json_encode($bindings), 'result' => json_encode($result)]);
        if ($result && $result->code === 'SUC') {
            return $result;
        }
        throw new InsException("Generate invoice/credit failed", 500);
    }
}