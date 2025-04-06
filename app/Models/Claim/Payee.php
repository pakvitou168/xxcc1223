<?php

namespace App\Models\Claim;

use App\Models\RefEnum;
use Illuminate\Database\Eloquent\Model;

class Payee extends Model
{
    protected $table = 'ins_claim_payee';

    protected static function booted()
    {
        static::addGlobalScope('active', function ($query) {
            $query->where('status', 'ACT');
        });

        static::creating(function ($obj) {
            $obj->status = 'ACT';
            $obj->created_by = auth()->id();
        });

        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }

    public function payeeType() {
        return $this->belongsTo(RefEnum::class, 'type', 'enum_id')
            ->where('type_id', 'PAYEE_TYPE')
            ->where('group_id', 'CLAIM_TYPE')
            ->where('status', 'ACT');
    }
}
