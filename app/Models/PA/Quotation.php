<?php

namespace App\Models\PA;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'ins_pa_quotation';
    protected $appends = [
        'issued_by'
    ];
    protected $fillable = [
        'approved_reason',
        'approved_status',
        'approved_by',
        'approved_at',
        'accepted_status',
        'accepted_by',
        'accepted_at',
        'accepted_reason'
    ];
    public function maker()
    {
        return $this->belongsTo(User::class, $this->updated_by ? 'updated_by' : 'created_by');
    }
    protected function issuedBy(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->maker()->value('full_name')
        );
    }
    public function policies()
    {
        return $this->hasMany(Policy::class, 'quotation_id');
    }
}
