<?php

namespace App\Models\Travel;

use App\Models\CustomerManagement\Customer;
use App\Models\Insurance\InsuranceClause;
use App\Models\Insurance\JointAccountDetail;
use App\Models\Product\Product;
use App\Models\RecordStatus;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class DataMaster extends Model
{
    use Compoships;
    protected $table = 'ins_tv_data_master';
    protected $fillable = [
        'status',
        'data_type',
        'product_code',
        'branch_code',
        'customer_no',
        'coverage_name',
        'insurance_period_type',
        'insurance_period_code',
        'insurance_period_val',
        'total_premium',
        'negotiation_rate',
        'remark',
        'joint_status',
        'insured_name',
        'insured_name_kh',
        'insured_name_zh',
        'business_code',
        'sale_channel',
        'commission_rate',
        'handler_code',
        'warranty',
        'warranty_kh',
        'memorandum',
        'memorandum_kh',
        'subjectivity',
        'subjectivity_kh',
        'policy_wording_version',
        'previous_id',
        'effective_date_from',
        'effective_date_to',
        'effective_month',
        'effective_day',
        'endorsement_e_date',
        'endos_day_remaining',
        'endorsement_type',
        'calc_option',
        'surcharge',
        'discount',
        'insured_person_note',
        'insured_person_note_kh',
        'remark_kh',
        'refund_option',
        'refund_percentage',
        'premium_amt_bf_refund',
        'custom_refund_amount',
        'package_code',
        'itinerary',
        'accumulation_limit',
        'created_by',
        'updated_by'
    ];
    protected function negotiationRate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value * 100,
            set: fn($value) => round($value / 100, 7)
        );
    }
    protected function commissionRate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value * 100,
            set: fn($value) => round($value / 100, 7)
        );
    }
    protected function surcharge(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value * 100,
            set: fn($value) => round($value / 100, 7)
        );
    }
    protected function discount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value * 100,
            set: fn($value) => round($value / 100, 7)
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }
    public function insuranceClauses()
    {
        return $this->belongsToMany(InsuranceClause::class, 'ins_tv_data_clause', 'data_id', 'clause_id');
    }
    public function dataDetails()
    {
        return $this->hasMany(DataDetail::class, 'master_data_id');
    }

    public function quotation()
    {
        return $this->hasOne(Quotation::class, 'data_id');
    }
    public function policy()
    {
        return $this->hasOne(Policy::class, 'data_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_no', 'customer_no');
    }
    public function insuredPersons()
    {
        return $this->hasMany(InsuredPersonV::class, 'data_id');
    }
    public function dataDetailsView()
    {
        return $this->hasMany(DataDetailV::class, 'data_id');
    }
    public function coverage()
    {
        return $this->hasMany(CoverageDataV::class, 'data_id');
    }

    public function insuranceData()
    {
        return $this->hasOne(DataMasterV::class, 'id');
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
