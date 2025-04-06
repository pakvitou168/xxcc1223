<?php

namespace App\Models\Insurance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Insurance\Auto;

class QuotationView extends Model
{
    protected $table = 'ins_quotation_v';

    public function quotation() {
        return $this->hasOne(Quotation::class, 'data_id', 'id');
    }

    public function auto(){
        return $this->hasOne(Auto::class, 'id', 'id');
    }

    /**
     * Check permission of Auto instead
     *
     * @return array
     */
    public function getUserPermissionsAttribute()
    {
        $auto = Auto::find($this->id);
        return [
            'VIEW' => auth()->user()->can('view', $auto),
            'UPDATE' => auth()->user()->can('update', $auto),
            'DELETE' => auth()->user()->can('delete', $auto),
            'REVISE' => auth()->user()->can('revise', $auto)
        ];
    }
}
