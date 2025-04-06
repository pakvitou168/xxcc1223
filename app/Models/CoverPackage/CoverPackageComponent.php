<?php

namespace App\Models\CoverPackage;

use Illuminate\Database\Eloquent\Model;

class CoverPackageComponent extends Model
{
    protected $table = 'ins_prod_cpkg_comp';

    protected $fillable = [
        'product_code',
        'cpkg_id',
        'comp_code',
    ];

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
}
