<?php

namespace App\Models\HS;

use App\Models\CustomerManagement\Customer;
use App\Models\Insurance\InsuranceClause;
use App\Models\Insurance\JointAccountDetail;
use App\Models\Product\Product;
use App\Models\UserManagement\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use KhmerDateTime\KhmerDateTime;

class DataMaster extends Model
{
	protected $table = 'ins_hs_data_master';
	protected $appends = ['issued_on'];
	protected $fillable = [
		'data_type',
		'product_code',
		'branch_code',
		'customer_no',
		'insurance_period_type',
		'insurance_period_code',
		'insurance_period_val',
		'total_premium',
		'negotiation_rate',
		'remark',
		'remark_kh',
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
		'effective_date_from',
		'effective_date_to',
		'endorsement_e_date',
		'endorsement_type',
		'calc_option',
		'surcharge',
		'discount',
		'ncd',
		'status',
		'insured_person_note',
		'insured_person_note_kh',
		'refund_option',
		'custom_refund_amount',
		'created_at',
		'created_by',
		'updated_at',
		'updated_by',
	];

	public function getIssuedOnAttribute()
	{
		return $this->updated_at ? $this->updated_at->format('d-M-Y') : ($this->created_at ? $this->created_at->format('d-M-Y') : null );
	}
	public function quotation()
	{
		return $this->hasOne(Quotation::class, 'data_id', 'id');
	}

	public function jointAccountDetails()
	{
		return $this->hasMany(JointAccountDetail::class, 'data_id', 'id');
	}

	public function insuranceClauses()
	{
		return $this->belongsToMany(InsuranceClause::class, 'ins_hs_data_clause', 'data_id', 'clause_id');
	}

	public function customer()
	{
		return $this->hasOne(Customer::class, 'customer_no', 'customer_no');
	}

	public function product()
	{
		return $this->belongsTo(Product::class, 'product_code', 'code');
	}

	public function policy()
	{
		return $this->hasOne(Policy::class, 'data_id', 'id');
	}

	public function issuedByName($issued_by_id)
	{
		return User::where('id', $issued_by_id)->value('full_name');
	}

	public function dataPlans()
	{
		return $this->hasManyThrough(PlanDataDetail::class, PlanData::class, 'master_data_id', 'plan_id', 'id', 'id');
	}

	public function dataPlansView()
	{
		return $this->hasMany(PlanDataDetailView::class, 'master_data_id');
	}

	public function dataSchema()
	{
		return $this->hasMany(SchemaDataView::class, 'master_data_id');
	}

	public function prevMaster()
	{
		return $this->belongsTo(DataMaster::class, 'previous_id')->where('data_type','POLICY');
	}

	public function hsDetails()
	{
		return $this->hasMany(DataDetail::class, 'master_data_id', 'id');
	}

	public function insuredPeriod()
	{
		$effectiveDateFrom = App::getLocale() !== 'km' ? Carbon::parse($this->effective_date_from)->format('d/F/Y') : KhmerDateTime::parse($this?->effective_date_from)->format('LL');
		$effectiveDateTo = App::getLocale() !== 'km' ? Carbon::parse($this->effective_date_to)->format('d/F/Y') : KhmerDateTime::parse($this?->effective_date_to)->format('LL');
		return $this->effective_day . " " . __('Days') . ' - ' . __('From') . ' ' . $effectiveDateFrom . ' ' . __('To') . ' ' . "$effectiveDateTo (" . __('Both Days Inclusive') . ')';
	}

	protected function endorsementDescription(): Attribute
	{
		return Attribute::make(
			get: fn() => $this->policy->endorsement_description,
		);
	}
	public function setCommissionRateAttribute($value)
	{
		$this->attributes['commission_rate'] = round($value / 100, 7);
	}
	public function getCommissionRateAttribute($value)
    {
        return round($value * 100, 7);
    }
	public function originMaster(){
		$master = $this->prevMaster ?? $this;
		$countNested = 0;
		while(!is_null($master->prevMaster) && $countNested < 500){
			$master = $master->prevMaster;
			$countNested++;
		}
		return $master;
	}
}
