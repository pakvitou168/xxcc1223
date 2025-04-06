<?php

namespace App\Models\Formula;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class ProductComponent extends Model
{
    protected $table = 'ins_prod_component';

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }
}
