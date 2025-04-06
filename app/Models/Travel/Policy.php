<?php

namespace App\Models\Travel;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $table = 'ins_tv_policy';
    protected $fillable = [
        'status',
        'policy_no',
        'document_no'
    ];

    public function maker()
    {
        return $this->belongsTo(User::class, $this->updated_by ? 'updated_by' : 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function dataMaster()
    {
        return $this->belongsTo(DataMaster::class, 'data_id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->created_by = auth()->id();
        });
        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }
}
