<?php

namespace App\Models\Deductible;

use App\Models\Cover\Cover;
use App\Models\Product\Product;
use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class Deductible extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'DEDUCTIBLE';
    
    protected $table = 'ins_prod_deductible';

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

    public function cover() {
        return $this->belongsTo(Cover::class, 'comp_code','code');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }
}
