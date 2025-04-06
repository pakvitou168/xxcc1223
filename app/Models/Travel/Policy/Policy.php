<?php

namespace App\Models\Travel\Policy;

use App\Models\Travel\Policy\Insurance\Endorsement;
use App\Models\Travel\Policy\Insurance\ReinsuranceData;
use App\Models\User;
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


    protected $table = 'ins_tv_policy';
    protected $fillable = [
        'status',
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
        'premium',
        'premium_adjustment',
        'policy_alt_no',
        'account_code',
        'handler_code',
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
        'renewal_reference_id',
    ];

    public function reinsuranceData()
    {
        return $this->hasMany(ReinsuranceData::class, 'policy_id', 'id');
    }
    public function maker()
    {
        return $this->belongsTo(User::class, $this->updated_by ? 'updated_by' : 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function dataMaster()
    {
        return $this->belongsTo(DataMaster::class, 'data_id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
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
            ->where('version', '!=', '0')
            ->count();
        if ($count == $endorsement->version) {
            return true;
        }
        return false;
    }


    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->created_by = auth()->id();
        });
        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }
}
