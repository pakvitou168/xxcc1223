<?php

namespace App\Models\Renewal;

use App\Models\Insurance\PolicyView;
use Illuminate\Database\Eloquent\Model;

class Renewal extends Model
{
    protected $table = 'ins_renewal_policy';

    const DRAFT = 'DRF';
    const PENDING = 'PND';
    const RENEWED = 'REW';
    const LOSS = 'LOS';
    const APPROVED = 'APV';
    const REJECTED = 'REJ';
    const DISABLED = 'DIS';

    protected $fillable = [
        'submit_status',
        'status',
        'approved_by',
        'approved_at',
        'approved_reason',
        'accept_status',
        'accepted_by',
        'accepted_at',
        'accepted_reason',
        'updated_at',
    ];

    public function policyView() {
        return $this->hasOne(PolicyView::class, 'id', 'reference_id');
    }

    protected static function booted()
    {
        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }
}
