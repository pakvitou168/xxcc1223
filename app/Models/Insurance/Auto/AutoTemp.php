<?php

namespace App\Models\Insurance\Auto;

use Illuminate\Database\Eloquent\Model;

class AutoTemp extends Model
{
    protected $table = 'ins_auto_data_detail_temp';
    protected $fillable = [
        'product_code',
        'master_data_type',
        'master_data_id',
        'model_id',
        'plate_no',
        'chassis_no',
        'engine_no',
        'manufacturing_year',
        'cubic',
        'vehicle_value',
        'passenger',
        'tonnage',
        'surcharge',
        'discount',
        'ncd',
        'negotiation_rate',
        'selected_cover_pkg',
        'remark',
        'commercial_vehicle_type',
        'vehicle_usage'
    ];

    public function getSurchargeAttribute($value)
    {
        return $value ? round($value * 100, 7) : null;
    }

    public function setSurchargeAttribute($value)
    {
        $this->attributes['surcharge'] = $value ? round($value / 100, 7) : null;
    }

    public function getDiscountAttribute($value)
    {
        return $value ? round($value * 100, 7) : null;
    }

    public function setDiscountAttribute($value)
    {
        $this->attributes['discount'] = $value ? round($value / 100, 7) : null;
    }

    public function getNcdAttribute($value)
    {
        return $value ? round($value * 100, 7) : null;
    }

    public function setNcdAttribute($value)
    {
        $this->attributes['ncd'] = $value ? round($value / 100, 7) : null;
    }

    public function getNegotiationRateAttribute($value)
    {
        return $value ? round($value * 100, 7) : null;
    }

    public function setNegotiationRateAttribute($value)
    {
        $this->attributes['negotiation_rate'] = $value ? round($value / 100, 7) : null;
    }

    public static function deleteTempData($master_data_id){
        AutoTemp::where('master_data_id', $master_data_id)->delete();
    }
}
