<?php

namespace App\Models\Insurance;

use App\Models\CustomerManagement\Customer;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product\Product;
use App\Models\UserPermissionTrait;
use App\Scopes\ActiveScope;
use App\Models\UserManagement\User\User;

class Auto extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'AUTO';

    protected $table = 'ins_auto_data_master';
    protected $casts = [
        'effective_date_from' => 'datetime:Y-m-d',
        'effective_date_to' => 'datetime:Y-m-d'
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

    public function getCommissionRateAttribute($value)
    {
        if($value == 0)
            return $value;
        return $value ? round($value * 100, 7) : null;
    }

    public function setCommissionRateAttribute($value)
    {
        if($value == 0)
            $this->attributes['commission_rate'] = $value;
        else
            $this->attributes['commission_rate'] = $value ? round($value / 100, 7) : null;
    }

    public function getNegotiationRateAttribute($value)
    {
        return $value ? round($value * 100, 7) : null;
    }

    public function setNegotiationRateAttribute($value)
    {
        $this->attributes['negotiation_rate'] = $value ? round($value / 100, 7) : null;
    }

    public function autoDetails() {
        return $this->hasMany(AutoDetail::class, 'master_data_id', 'id');
    }

    public function allAutoDetails() {
        return $this->hasMany(AutoDetail::class, 'master_data_id', 'id')->withoutGlobalScope(ActiveScope::class);
    }

    public function quotation() {
        return $this->hasOne(Quotation::class, 'data_id', 'id');
    }

    public function insuranceClauses()
    {
        return $this->belongsToMany(InsuranceClause::class, 'ins_auto_data_clause', 'data_id', 'clause_id');
    }

    public function customers() {
        return $this->hasMany(Customer::class, 'customer_no', 'customer_no');
    }
    public function customer() {
        return $this->hasOne(Customer::class, 'customer_no', 'customer_no');
    }

    public  function product()
    {
        return $this->hasOne(Product::class, 'code', 'product_code');
    }

    public function customerClassification(){
        return $this->hasOne(Customer::class, 'cust_classification','cust_classification');
    }

    public function jointAccountDetails() {
        return $this->hasMany(JointAccountDetail::class, 'data_id', 'id');
    }

    public function policy() {
        return $this->hasOne(BasePolicy::class, 'data_id', 'id');
    }

    public function issuedByName($issued_by_id) {
        return User::where('id', $issued_by_id)->value('full_name');
    }

    public function hasPendingPolicy() {
        $currentLineQuotationNo = optional($this->quotation)->quotation_no;

        $policy = BasePolicy::whereHas('quotation', function ($query) use ($currentLineQuotationNo) {
            $query->where('quotation_no', $currentLineQuotationNo);
        })->first();
        
        if ($policy) return true;
        return false;
    }
}
