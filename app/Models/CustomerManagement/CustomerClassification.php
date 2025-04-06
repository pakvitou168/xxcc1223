<?php

namespace App\Models\CustomerManagement;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class CustomerClassification extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'CUSTOMER_CLASSIFICATION';

    // use HasFactory;
    protected $table = 'ins_cust_classification';

    protected $primaryKey = 'cust_classification';
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'cust_classification', 'cust_classification');
    }
}
