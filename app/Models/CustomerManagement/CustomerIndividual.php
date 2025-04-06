<?php

namespace App\Models\CustomerManagement;

use Illuminate\Database\Eloquent\Model;

class CustomerIndividual extends Model
{
    protected $table = 'ins_cust_individual';

    protected $attributes = [
        'status' => 'ACT',
    ];

    protected $fillable = ['customer_no'];

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

    public function customer()
    {
        return $this->hasOne(Customer::class, 'customer_no', 'customer_no');
    }
}
