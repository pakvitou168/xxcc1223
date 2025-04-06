<?php

namespace App\Models\HS;

use App\Models\HS\Insurance\Endorsement;
use App\Models\HS\Insurance\ReinsuranceData;
use App\Models\UserManagement\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    const PENDING = 'PND';
    const APPROVED = 'APV';
    const REJECTED = 'REJ';

    const GENERAL_ENDORSEMENT = 'GENERAL';
    const ADD_DELETE_ENDORSEMENT = 'ADD/DELETE';
    const CANCELLATION_ENDORSEMENT = 'CANCELLATION';

    protected $table = 'ins_hs_policy';

    protected $fillable = [
        'policy_no',
        'version',
        'cycle',
        'document_no',
        'quotation_id',
        'branch_code',
        'customer_no',
        'product_line_code',
        'product_code',
        'data_id',
        'sum_insured',
        'premium',
        'policy_alt_no',
        'account_code',
        'handler_code',
        'status',
        'created_by',
        'updated_by',
        'approved_at',
        'approved_by',
        'business_type',
        'policy_type',
        'approved_status',
        'approved_reason',
        'endorsement_description',
        'request_amount',
        'renewal_reference_id'
    ];

    public function reinsuranceData()
    {
        return $this->hasMany(ReinsuranceData::class, 'policy_id', 'id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    public function issuedByName($issued_by_id)
    {
        return User::where('id', $issued_by_id)->value('full_name');
    }

    public function dataMaster()
    {
        return $this->belongsTo(DataMaster::class, 'data_id');
    }

    public function insuredPersons()
    {
        return $this->hasMany(DataDetail::class, 'master_data_id', 'data_id');
    }

    public function insuredPersonsV()
    {
        return $this->hasMany(DataDetailView::class, 'master_data_id', 'data_id');
    }

    public function schemaData()
    {
        return $this->hasMany(SchemaData::class, 'master_data_id', 'data_id');
    }

    public function dataPlans()
    {
        return $this->hasMany(PlanDataDetailView::class, 'master_data_id', 'data_id');
    }

    public function isPolicyConfigurationCompleted()
    {
        if (!$this->business_type || !$this->policy_type) {
            return false;
        }
        return true;
    }

    public static function isLatestEndorsement(Endorsement $endorsement)
    {
        $count = Endorsement::where('quotation_id', $endorsement->quotation_id)
            ->whereNotNull('version')
            ->count();
        if ($count == $endorsement->version) {
            return true;
        }
        return false;
    }
}
