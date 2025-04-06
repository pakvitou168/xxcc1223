<?php

namespace App\Models\Travel\Policy\ReinsuranceConfig;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class ReinsuranceConfig extends Model
{
  use UserPermissionTrait;
  static $functionCode = 'REINSURANCE_CONFIG';
  protected $table = 'ins_tv_reinsurance_config';

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

  public function getShareAttribute($value)
  {
      return $value ? round($value * 100, 7) : null;
  }
  public function setShareAttribute($value)
  {
    $this->attributes['share'] = $value ? round($value / 100, 7) : null;
  }

  public function getRiCommissionAttribute($value)
  {
    return $value ? round($value * 100, 7) : null;
  }

  public function setRiCommissionAttribute($value)
  {
    $this->attributes['ri_commission'] = $value ? round($value / 100, 7) : null;
  }

  public function getTaxFeeAttribute($value)
  {
    return $value ? round($value * 100, 7) : null;
  }

  public function setTaxFeeAttribute($value)
  {
    $this->attributes['tax_fee'] = $value ? round($value / 100, 7) : null;
  }
}
