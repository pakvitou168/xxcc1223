<?php

namespace App\Http\Controllers\ProductConfiguration\ExchangeRate;

use App\Models\Branch;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\ProductConfiguration\ExchangeRate;

class ExchangeRateController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(ExchangeRate::class, 'exchange_rate');
    }

    public function index() {
        return $this->generateTableData(
            ExchangeRate::with(
                ['branch' => function($query) {$query->select('code', 'name');}]
            )
            ->whereIn('status',['ACT','PND','REJ'])->orderByDesc('id')
        );
    }

    public function create() {
        //
    }

    public function store(Request $request) {
        try {
            $this->validateRequest($request);
            $exchangeRate = new ExchangeRate();
            $exchangeRate->status = 'PND';
            $this->assignValues($request,$exchangeRate);
            if ($exchangeRate->save())
                return response()->json('Exchange is successfully created.');
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }

    public function show(ExchangeRate $exchangeRate) {
        if($exchangeRate->status != 'DEL') {
            $exchangeRate->branch = Branch::select('code', 'name')->where('code', $exchangeRate->branch_code)->first();
            $exchangeRate->rate_date = \Carbon\Carbon::parse($exchangeRate->rate_date)->format('d M Y');
            return $exchangeRate;
        }
        else
            return [
                'error' => true,
                'message' => "Sorry, can't find a record."
            ];
    }

    public function edit(ExchangeRate $exchangeRate) {
        if($exchangeRate->status != 'DEL') {
            return $exchangeRate;
        }
        else
            return [
                'error' => true,
                'message' => "Sorry, can't find a record."
            ];
    }

    public function update(Request $request, ExchangeRate $exchangeRate) {
        try {
            $this->validateRequest($request);
            $this->assignValues($request,$exchangeRate);
            if ($exchangeRate->update()){
                return [
                    'success' => true,
                    'message' => 'Record is updated.'
                ];
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }

    public function destroy(ExchangeRate $exchangeRate) {
        $exchangeRate->status = "DEL";
        if ($exchangeRate->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }

    private function assignValues(Request $request,ExchangeRate $exchangeRate) {
        $exchangeRate->branch_code = $request->branch_code;
        $exchangeRate->rate_type = $request->rate_type;
        $exchangeRate->rate_date = $request->rate_date;
        $exchangeRate->ccy1 = $request->ccy1;
        $exchangeRate->ccy2 = $request->ccy2;
        $exchangeRate->mid_rate = $request->mid_rate;
        $exchangeRate->buy_rate = $request->buy_rate;
        $exchangeRate->sale_rate = $request->sale_rate;
    }

    private function validateRequest(Request $request) {
        $validator = Validator::make($request->all(), [
            'branch_code' => ['required'],
            'rate_type' => ['required'],
            'rate_date' => ['required'],
            'ccy1' => ['required'],
            'ccy2' => ['required'],
            'mid_rate' => ['required'],
        ]);

        if ($validator->fails()) {
            Log::error('ExchangeRateController - Incorrect validate data input : ' . $validator->errors());
            throw new ValidationException($validator,'Validate data input was incorrectly!');
        }
    }
}
