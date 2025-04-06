<?php

namespace App\Models\CompFrmExpression;

use App\Models\Formula\ProductComponent;
use App\Models\Product\Product;
use App\Models\Formula\Formula;
use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class CompFrmExpression extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'COMP_FRM_EXPRESSION';
    
    protected $table = 'ins_prod_comp_frm_expr';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });
        
    }

    public function prod_comp() {
        return $this->belongsTo(ProductComponent::class, 'component_code', 'code');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }

    public function getFormula() {
        return $this->belongsTo(Formula::class, 'formula_code', 'formula_code');
    }
}
