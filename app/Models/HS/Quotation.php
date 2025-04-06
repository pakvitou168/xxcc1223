<?php

namespace App\Models\HS;

use App\Models\CustomerManagement\Customer;
use App\Models\UserManagement\User\User;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    const PENDING = 'PND';
    const APPROVED = 'APV';
    const REJECTED = 'REJ';

    protected $table = 'ins_hs_quotation';

    protected $fillable = [
        'approved_status',
        'approved_by',
        'approved_at',
        'approved_reason',
        'accepted_status',
        'accepted_by',
        'accepted_at',
        'accepted_reason',
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class, 'customer_no', 'customer_no');
    }

    public function policy()
    {
        return $this->hasOne(Policy::class, 'quotation_id', 'id');
    }

    public function issuedByName($issued_by_id)
    {
        return User::where('id', $issued_by_id)->value('full_name');
    }

    public function insuredPersons()
    {
        return $this->hasMany(DataDetail::class, 'master_data_id','data_id');
    }
}
