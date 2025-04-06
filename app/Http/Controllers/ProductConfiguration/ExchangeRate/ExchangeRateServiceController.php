<?php

namespace App\Http\Controllers\ProductConfiguration\ExchangeRate;

use App\Models\Branch;
use App\Models\RefEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductConfiguration\ExchangeRate;
use Illuminate\Support\Facades\Auth;
use App\Models\UserManagement\User\UserBranch;

class ExchangeRateServiceController extends Controller
{

    public function getBranch() {
        $branch = Branch::getBranch();
        return $branch;
    }

    public function getCurrency()
    {
        $currency = RefEnum::getCurrency();
        $formattedCurrency = [];
        foreach ($currency as $key => $value) {
            $formattedCurrency[] = [
                'value' => $value, 
                'label' => $value   
            ];
        }
        return $formattedCurrency;
    }

    public function getRateType() {
        $rateType = RefEnum::getRateType();
        return $rateType;
    }

    public function approve(Request $request, ExchangeRate $exchangeRate) {

        $this->authorize('approve', ExchangeRate::class);
        if($exchangeRate->status === 'PND') {
            $exchangeRate->status = $request->post('approved_status');
            if($request->post('approved_status') === 'ACT'){
                $exchangeRate->approved_at = now();
                $exchangeRate->approved_by = auth()->id();
            }

            if ($exchangeRate->save()) {
                return response ([
                    'success' => true,
                    'message' => 'Record Updated successfully'
                ]);
            }
            else
                return response ([
                    'error' => true,
                    'message' => 'Something wrong!'
                ]);
        }
        else
            return response ([
                'error' => true,
                'message' => 'Approved already!'
            ]);
    }


    public function getLatestExchangeRate(){
        $latestExchangeRate = ExchangeRate::getLatestExchangeRate(UserBranch::where('user_id', Auth::user()->id)->value('branch_code'));
        $latestExchangeRate->rate_date = \Carbon\Carbon::parse($latestExchangeRate->rate_date)->format('d M Y');
        return $latestExchangeRate;
    }
}
