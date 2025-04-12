<?php

namespace App\Models\Travel\Policy;

use App\Models\Travel\Policy\Insurance\Endorsement;
use App\Models\Travel\Policy\Insurance\ReinsuranceData;
use App\Models\Travel\Policy\Invoice\InvoiceNote;
use App\Models\User;
use App\Models\UserManagement\User\UserFile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use KhmerDateTime\KhmerDateTime;

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

    public function invoiceNote()
    {
        return $this->hasOne(InvoiceNote::class, 'policy_id');
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

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\UserManagement\User\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function issuedByName($issued_by_id) {
        return User::where('id', $issued_by_id)->value('full_name');
    }

    public function insuredPeriod()
    {
        $effectiveDateFrom = App::getLocale() !== 'km' ? Carbon::parse($this->effective_date_from)->format('d/F/Y') : KhmerDateTime::parse($this?->effective_date_from)->format('LL');
        $effectiveDateTo = App::getLocale() !== 'km' ? Carbon::parse($this->effective_date_to)->format('d/F/Y') : KhmerDateTime::parse($this?->effective_date_to)->format('LL');
        return $this->effective_day . " " . __('Days') . ' - ' . __('From') . ' ' . $effectiveDateFrom . ' ' . __('To') . ' ' . "$effectiveDateTo (" . __('Both Days Inclusive') . ')';
    }
    public function address()
    {
        $address = null;
        if ($this->dataMaster && $customer = $this->dataMaster->customer) {
            $getCustomerInfo = collect(DB::select('select * from ins_get_customer_info(?)', [ $customer->customer_no]))->first();
            if($getCustomerInfo) {
                $address = $getCustomerInfo->address;
            }
        }
        return $address;
    }

    public function signature($withSignature = null)
    {
        $signature = null;
        if ($this->status == 'APV' && $withSignature) {
            $signature = UserFile::select('file_url')
                ->where('user_id', $this->approved_by)
                ->where('file_type', 'SIGNATURE')
                ->first();

            if ($signature && empty($signature->file_url)) {
                $signature = null;
            }
        }

        return $signature;
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
