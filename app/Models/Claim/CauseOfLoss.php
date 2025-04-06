<?php

namespace App\Models\Claim;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class CauseOfLoss extends Model
{
    protected $table = 'ins_claim_cause_of_loss';

    protected static function booted()
    {
        static::addGlobalScope('active', function ($query) {
            $query->where('status', 'ACT');
        });

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
}
