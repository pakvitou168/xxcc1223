<?php

namespace App\Models\Insurance\Endorsement;

use App\Models\Insurance\AutoDetail;
use App\Scopes\EndorsementScope;
use Illuminate\Database\Eloquent\Model;

class EndorsementView extends Model
{
    protected $table = 'ins_policy_v';

    protected static function booted()
    {
        // Scope for policy
        static::addGlobalScope(new EndorsementScope);
    }

    public function endorsement() {
        return $this->belongsTo(Endorsement::class, 'id', 'id');
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
        $endorsement = Endorsement::find($this->id);
        return [
            'VIEW' => auth()->user()->can('view', $endorsement),
            'UPDATE' => auth()->user()->can('update', $endorsement),
            'DELETE' => auth()->user()->can('delete', $endorsement),
            'REVISE' => auth()->user()->can('revise', $endorsement)
        ];
    }
}
