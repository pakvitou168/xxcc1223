<?php

namespace App\Models\CustomerManagement;

use App\Models\Address\AddressCode;
use App\Models\UserPermissionTrait;
use Awobaz\Compoships\Compoships;
use DB;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use UserPermissionTrait, Compoships;

    static $functionCode = 'CUSTOMER';

    protected $table = 'ins_customer';

    protected $attributes = [
        'branch_code' => '000',
        'status' => 'ACT',
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
    public function getNameEnAttribute($value)
    {
        return $value ?: '';
    }
    public function getNameKhAttribute($value)
    {
        return $value ?: '';
    }
    public function customerIndividual()
    {
        return $this->hasOne(CustomerIndividual::class, 'customer_no', 'customer_no');
    }

    public function customerCorporate()
    {
        return $this->hasOne(CustomerCorporate::class, 'customer_no', 'customer_no');
    }

    public function customerClassification()
    {
        return $this->hasOne(CustomerClassification::class, 'cust_classification', 'cust_classification');
    }
    public function classification()
    {
        return $this->belongsTo(CustomerClassification::class, 'cust_classification', 'cust_classification');
    }
    public function customerContacts()
    {
        return $this->hasMany(CustomerContact::class, 'customer_no', 'customer_no');
    }

    public static function getCustomerTypeByCustomerNo($customerNo)
    {
        return Customer::where('customer_no', $customerNo)->value('customer_type');
    }

    public static function getCustomerClassificationDesc($cust_classification)
    {
        return CustomerClassification::where('cust_classification', $cust_classification)
            ->where('status', 'ACT')
            ->value('description');
    }

    public static function listCustomersByType($type)
    {
        return Customer::select('name_en', 'customer_no')->where('customer_type', $type)
            ->where('status', 'ACT')->orderBy('customer_no')->get()->map(function ($item) {
                return [
                    'label' => $item->customer_no . ' - ' . $item->name_en,
                    'value' => $item->customer_no
                ];
            });
    }

    public function sourceAddress()
    {
        return $this->belongsTo(AddressCode::class, 'postal_code', 'postal_code');
    }

    public function getCorrespondenceAddressAttribute($value)
    {
        return $this->info()?->address;
    }
    public function info()
    {
        return collect(DB::select('select * from ins_hs_get_customer_info(?)', [$this->customer_no]))->first();
    }
}
