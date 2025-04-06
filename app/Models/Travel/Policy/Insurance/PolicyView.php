<?php

namespace App\Models\Travel\Policy\Insurance;

use App\Models\Travel\Policy\DataDetail;
use App\Models\Travel\Policy\Policy;
use App\Scopes\Travel\PolicyScope;
use Illuminate\Database\Eloquent\Model;

class PolicyView extends Model
{
    protected $table = 'ins_tv_policy_v';

    protected static function booted()
    {
        // Scope for policy
        static::addGlobalScope(new PolicyScope);
    }

    public function policy() {
        return $this->belongsTo(Policy::class, 'id', 'id');
    }

    public function DataDetails() {
        return $this->hasMany(DataDetail::class, 'master_data_id', 'data_id');
    }
}
