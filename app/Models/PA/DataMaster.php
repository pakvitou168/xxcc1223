<?php

namespace App\Models\PA;

use App\Enums\RecordStatus;
use App\Models\CustomerManagement\Customer;
use App\Models\Insurance\InsuranceClause;
use App\Models\Insurance\JointAccountDetail;
use App\Models\Product\Product;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class DataMaster extends Model
{
    use Compoships;
    protected $table = 'ins_pa_data_master';
    protected $fillable = [
        'status',
        'data_type',
        'product_code',
        'branch_code',
        'customer_no',
        'coverage_id',
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
        'accumulation_limit_amount'
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
    protected function accumulationLimitAmount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => floatval($value),
            set: fn($value) => floatval($value)
        );
    }
    protected function warranty(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? '',
            set: fn($value) => $value
        );
    }
    protected function warrantyKh(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? '',
            set: fn($value) => $value ?? ''
        );
    }
    protected function memorandum(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? '',
            set: fn($value) => $value
        );
    }
    protected function memorandumKh(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? '',
            set: fn($value) => $value ?? ''
        );
    }
    protected function subjectivity(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? '',
            set: fn($value) => $value
        );
    }
    protected function subjectivityKh(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? '',
            set: fn($value) => $value ?? ''
        );
    }
    protected function remark(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? '',
            set: fn($value) => $value
        );
    }
    protected function remarkKh(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? '',
            set: fn($value) => $value ?? ''
        );
    }
    protected function insuredPersonNote(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? '',
            set: fn($value) => $value
        );
    }
    protected function insuredPersonNoteKh(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? '',
            set: fn($value) => $value ?? ''
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }
    public function jointAccountDetails()
    {
        return $this->hasMany(JointAccountDetail::class, ['data_id', 'product_code'], ['id', 'product_code']);
    }
    public function insuranceClauses()
    {
        return $this->belongsToMany(InsuranceClause::class, 'ins_pa_data_clause', 'data_id', 'clause_id');
    }
    public function dataDetails()
    {
        return $this->hasMany(DataDetail::class, 'master_data_id');
    }
    public function extensions()
    {
        return $this->belongsToMany(BnfExtension::class, BnfExtensionData::class, 'data_id', 'extension_id')->using(BnfExtensionPV::class)->withPivot('rating');
    }
    public function optionalExtensions()
    {
        return $this->hasMany(BnfExtensionV::class, 'data_id')->whereIn('extension_code', $this->extensions()->pluck('extension_code')->toArray());
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
    public function coverage()
    {
        return $this->belongsTo(Coverage::class, 'coverage_id');
    }
    public function reinsurances()
    {
        return $this->hasMany(Reinsurance::class, 'data_id');
    }
    public function endorsement()
    {
        return $this->hasOne(DataMaster::class, 'previous_id', 'id')->whereStatus(RecordStatus::ACTIVE);
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
