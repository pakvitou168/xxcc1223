<?php

namespace App\Models\Travel\Policy\Invoice;

use Illuminate\Database\Eloquent\Model;

class InvoiceNote extends Model
{
    protected $table = 'ins_tv_policy_invoice_note';

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

    public static function getInvoiceData($policyId) {
        // If invoice is generated
        if (InvoiceNote::where('policy_id', $policyId)->exists())
            return InvoiceNote::where('policy_id', $policyId)->where('status', 'ACT')->first();

        return InvoiceView::where('policy_id', $policyId)->first();
    }
}
