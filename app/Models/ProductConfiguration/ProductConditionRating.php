<?php

namespace App\Models\ProductConfiguration;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductConditionRating extends Model
{
  use HasFactory;
  protected $table = 'ins_prod_cond_rating';

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

  protected $fillable = [
    'product_code',
    'code',
    'description',
    'key',
    'value',
    'cond_expr',
    'cond_type'
  ];
}
