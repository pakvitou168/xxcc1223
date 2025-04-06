<?php

namespace App\Models\Insurance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Insurance\Quotation;

class BasePolicy extends Model
{
    protected $table = 'ins_policy';

    public function reinsuranceData() {
        return $this->hasMany(ReinsuranceData::class, 'policy_id', 'id');
    }

    public function quotation() {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    /**
     * Check if business_type and policy_type exists
     *
     * @return boolean
     */
    public function isPolicyConfigurationCompleted() {
        if (!$this->business_type || !$this->policy_type) return false;
        return true;
    }

    public function auto() {
        return $this->belongsTo(Auto::class, 'data_id', 'id');
    }

    public static function isLatestEndorsement(BasePolicy $policy) {
        $count = BasePolicy::where('quotation_id', $policy->quotation_id)
            ->whereNotNull('version')
            ->count();
        if ($count == $policy->version) return true;

        return false;
    }
}
