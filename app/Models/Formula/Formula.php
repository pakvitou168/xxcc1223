<?php

namespace App\Models\Formula;

use App\Models\Product\Product;
use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'COMP_FORMULA';
    
    protected $table = 'ins_prod_comp_formula';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
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

    public function prodComp() {
        return $this->belongsTo(ProductComponent::class, 'component_code', 'code');
    }
}
