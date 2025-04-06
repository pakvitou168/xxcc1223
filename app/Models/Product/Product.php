<?php

namespace App\Models\Product;

use App\Models\ProductLine\ProductLine;
use App\Models\UserPermissionTrait;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'PRODUCT';

    protected $table = 'ins_product';

    protected static function booted()
    {
        static::addGlobalScope(new ActiveScope);

        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });
    }

    public function productLine()
    {
        return $this->belongsTo(ProductLine::class, 'product_line_code', 'code');
    }

    public function getDefaultSurchargeAttribute($value)
    {
        // if value is null, return empty string to display default surchage in form
        return $value ? round($value * 100, 7) : '';
    }

    public function setDefaultSurchargeAttribute($value)
    {
        $this->attributes['default_surcharge'] = $value ? round($value / 100, 7) : null;
    }

    public function getDefaultDiscountAttribute($value)
    {
        // if value is null, return empty string to display default discount in form
        return $value ? round($value * 100, 7) : '';
    }

    public function setDefaultDiscountAttribute($value)
    {
        $this->attributes['default_discount'] = $value ? round($value / 100, 7) : null;
    }

    public function getDefaultNcdAttribute($value)
    {
        // if value is null, return empty string to display default discount in form
        return $value ? round($value * 100, 7) : '';
    }

    public function setDefaultNcdAttribute($value)
    {
        $this->attributes['default_ncd'] = $value ? round($value / 100, 7) : null;
    }

    public static function listAutoProducts() {
        return Product::select('name', 'code', 'description')
            ->where('status', 'ACT')
            ->where('product_line_code', 'AUTO')
            ->orderBy('code')
            ->get()
            ->map(function($item) {
                return [
                    'label' => $item->code . ' - ' . $item->name,
                    'value' => $item->code,
                    'desc' => $item->description,
                ];
            });
    }

    public static function getProductSpecificationByCode($code) {
        return Product::where('code', $code)->value('prod_specification');
    }

    public static function listProductsByProductLine($productLineCode) {
        return Product::select('name', 'code', 'description')
            ->where('status', 'ACT')
            ->where('product_line_code', $productLineCode)
            ->orderBy('code')
            ->get()
            ->map(function($item) {
                return [
                    'label' => $item->code . ' - ' . $item->name,
                    'value' => $item->code,
                    'desc' => $item->description,
                ];
            });
    }

    public static function getDefaultSurchargeByProductCode($code){
        return Product::where('code', $code)->value('default_surcharge');
    }

    public static function getDefaultDiscountByProductCode($code){
        return Product::where('code', $code)->value('default_discount');
    }

    public static function getDefaultNCDByProductCode($code){
        return Product::where('code', $code)->value('default_ncd');
    }

    public static function findByCode(string $code, string $field = '') {
        $query = Product::where('code', $code)->where('status', 'ACT');
        if ($field) {
            return $query->value($field);
        }

        return $query->first();
    }
}

