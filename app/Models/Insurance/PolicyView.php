<?php

namespace App\Models\Insurance;

use App\Scopes\PolicyScope;
use Illuminate\Database\Eloquent\Model;

class PolicyView extends Model
{
    protected $table = 'ins_policy_v';

    protected static function booted()
    {
        // Scope for policy
        static::addGlobalScope(new PolicyScope);
    }

    public function policy() {
        return $this->belongsTo(Policy::class, 'id', 'id');
    }

    public function autoDetails() {
        return $this->hasMany(AutoDetail::class, 'master_data_id', 'data_id');
    }

    /**
     * Check permission of Policy instead
     *
     * @return array
     */
    public function getUserPermissionsAttribute()
    {
        $policy = Policy::find($this->id);
        return [
            'VIEW' => auth()->user()->can('view', $policy),
            'UPDATE' => auth()->user()->can('update', $policy),
            'DELETE' => auth()->user()->can('delete', $policy),
            'REVISE' => auth()->user()->can('revise', $policy)
        ];
    }
}
