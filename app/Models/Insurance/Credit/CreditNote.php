<?php

namespace App\Models\Insurance\Credit;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $table = 'ins_policy_invoice_note';

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
            $obj->created_by = auth()->id();
        });

        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }
}
