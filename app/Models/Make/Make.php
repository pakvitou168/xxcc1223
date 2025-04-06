<?php

namespace App\Models\Make;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class Make extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'MAKE';
    
    protected $table = 'ins_lov_vehicle_make';

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

    public function model()
    {
        return $this->hasMany(MakeModel::class, 'make_id', 'id');
    }
}
