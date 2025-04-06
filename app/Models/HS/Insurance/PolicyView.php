<?php

namespace App\Models\HS\Insurance;

use App\Models\HS\DataDetail;
use App\Models\HS\Policy;
use App\Scopes\PolicyScope;
use Illuminate\Database\Eloquent\Model;

class PolicyView extends Model
{
    protected $table = 'ins_hs_policy_v';

    protected static function booted()
    {
        // Scope for policy
        static::addGlobalScope(new PolicyScope);
    }

    public function policy() {
        return $this->belongsTo(Policy::class, 'id', 'id');
    }

    public function hsDetails() {
        return $this->hasMany(DataDetail::class, 'master_data_id', 'data_id');
    }
}
