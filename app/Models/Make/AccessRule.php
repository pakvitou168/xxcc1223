<?php

namespace App\Models\Make;

use App\Models\User;
use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class AccessRule extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'ACCESS_RULE';
    
    protected $table = 'ins_lov_vehicle_rules';

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

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function make() {
        return $this->belongsTo(Make::class, 'make_id', 'id');
    }

    public function model() {
        return $this->belongsTo(MakeModel::class, 'model_id', 'id');
    }
}
