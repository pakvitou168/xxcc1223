<?php

namespace App\Models\PA;

use App\Models\RecordStatus;
use App\Models\ReinsuranceConfig\ReinsurancePartner;
use App\Models\ReinsuranceConfig\ReinsurancePartnerGroup;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Reinsurance extends Model
{
    protected $table = 'ins_pa_reinsurance_data';

    protected $fillable = ['policy_id','data_id','product_line_code','product_code','treaty_code','lvl','parent_code','share','ri_commission','tax_fee','endorsement_stage','endorsement_state','created_by','updated_by'];

    protected function share(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value * 100,
            set: fn($value) => round($value / 100, 7)
        );
    }
    protected function riCommission(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value * 100,
            set: fn($value) => round($value / 100, 7)
        );
    }
    protected function taxFee(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value * 100,
            set: fn($value) => round($value / 100, 7)
        );
    }
    public function subs()
    {
        return $this->hasMany(Reinsurance::class, 'parent_code', 'treaty_code');
    }
    public function partner()
    {
        return $this->belongsTo(ReinsurancePartner::class, 'treaty_code', 'code');
    }
    public function type()
    {
        return $this->belongsTo(Reinsurance::class, 'partner_code', 'treaty_code');
    }
    public function group()
    {
        return $this->hasOneThrough(ReinsurancePartnerGroup::class, ReinsurancePartner::class, 'code', 'code', 'treaty_code', 'group_code');
    }

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = RecordStatus::ACTIVE;
            $obj->created_by = auth()->id();
        });
        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }
}
