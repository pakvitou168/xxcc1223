<?php

namespace App\Models\ProductConfiguration;

use App\Models\Product\Product;
use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class PolicyWordingVersion extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'POLICY_WORDING_VERSION';
    
    protected $table = 'ins_policy_wording_version';

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
            $obj->created_by = auth()->id();
        });

        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }

    public static function listByProductCode($productCode) {
        return PolicyWordingVersion::where('product_code', $productCode)
            ->where('status', 'ACT')
            ->select('policy_wording AS label', 'policy_wording AS value')->get();
    }
}
