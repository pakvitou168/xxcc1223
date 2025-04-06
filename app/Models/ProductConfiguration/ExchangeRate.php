<?php

namespace App\Models\ProductConfiguration;

use App\Models\Branch;
use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExchangeRate extends Model
{
    use HasFactory;
    use UserPermissionTrait;

    static $functionCode = 'EXCHANGE_RATE';
    protected $table = 'fn_exchange_rate';

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'PND';
            $obj->created_by = auth()->id();
        });

        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }

    public function getMidRateAttribute($value)
    {
        return $value ? number_format($value, 2) : null;
    }

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_code', 'code');
    }

    public static function getLatestExchangeRate($branch_code){
        return ExchangeRate::where('branch_code', $branch_code)
                        ->where('rate_type', 'STANDARD')
                        ->where('ccy1','USD')
                        ->where('ccy2', 'KHR')
                        ->where('status','ACT')
                        ->whereDate('rate_date', '<=', \Carbon\Carbon::now())
                        ->latest('rate_date')
                        ->first();
    }
}
