<?php

namespace App\Models\UserManagement\User;

use App\Models\UserManagement\Functions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFunction extends Model
{
    use HasFactory;
    protected $table = 'idm_user_func';

    public function fnc()
    {
        return $this->belongsTo(Functions::class, 'function_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    protected static function booted()
    {
        static::creating(function ($func) {
            $func->status = 'ACT';
            $func->create_by = auth()->user()->id;
            $func->create_at = date('Y-m-d H:i:s');
        });

        static::updated(function ($func) {
            $func->update_by = auth()->user()->id;
            $func->update_at = date('Y-m-d H:i:s');
        });
    }
}
