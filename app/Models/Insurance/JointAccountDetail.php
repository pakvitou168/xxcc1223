<?php

namespace App\Models\Insurance;

use App\Models\CustomerManagement\Customer;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class JointAccountDetail extends Model
{
    use Compoships;
    protected $table = 'ins_joint_account_detail';

    protected $fillable = [
        'customer_no',
        'product_line_code',
        'product_code',
        'data_id',
        'joint_level',
        'permission'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });

    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'customer_no', 'customer_no');
    }
}
