<?php

namespace App\Models\CoverPackage;

use App\Models\Product\Product;
use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class CoverPackage extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'COVER_PACKAGE';
    
    protected $table = 'ins_prod_cover_package';

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

    public function coverPackageComponents() {
        return $this->hasMany(CoverPackageComponent::class, 'cpkg_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }

    public static function getCoverPackageWithRemainingCovers($packageId, $selectedCovers) {
        $coverPackage = CoverPackage::find($packageId);
        if ($packageId) {
            $packageCovers = $coverPackage->coverPackageComponents()->pluck('comp_code');
            $remainCoversArr = collect($selectedCovers)->diff($packageCovers)->values()->toArray();
            
            if (!empty($remainCoversArr))
                return $coverPackage->name.'+'.implode('+', $remainCoversArr);
            return $coverPackage->name;
            
        }
        return implode('+', $selectedCovers);

    }
}
