<?php

namespace App\Models\CustomerManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model
{
    use HasFactory;
    protected $table = 'ins_cust_contact';

    protected $attributes = [
        'status' => 'ACT',
    ];

    protected $fillable = [
        'customer_no',
        'contact_level',
        'contact_type',
        'contact_info',
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
