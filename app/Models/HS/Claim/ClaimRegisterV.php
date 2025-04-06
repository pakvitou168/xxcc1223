<?php

namespace App\Models\HS\Claim;

use App\Models\HS\DataDetail;
use App\Models\HS\DataMaster;
use App\Models\HS\Reinsurance\ClaimReinsurance;
use Illuminate\Database\Eloquent\Model;

class ClaimRegisterV extends Model
{
    protected $table = 'ins_hs_list_claim_v';
    protected $primaryKey = 'claim_id';

    public function insuredPerson()
    {
        return $this->belongsTo(DataDetail::class, 'data_detail_id');
    }

    public function dataMaster()
    {
        return $this->belongsTo(DataMaster::class, 'data_id');
    }
    public function reinsurances()
    {
        return $this->hasMany(ClaimReinsurance::class, 'claim_no', 'claim_no');
    }
    public function detail()
    {
        return $this->belongsTo(ClaimRegisterDetail::class, 'claim_detail_id');
    }
    public function schema()
    {
        return $this->detail();
    }
}
