<?php

namespace App\Models\ClauseMaintenance;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class ClauseMaintenance extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'CLAUSE_MAINTENANCE';
    
    protected $table = 'ins_insurance_clause';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
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
