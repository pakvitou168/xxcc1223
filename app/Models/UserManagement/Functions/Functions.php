<?php

namespace App\Models\UserManagement\Functions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Functions extends Model
{
    use HasFactory;
    protected $table = "idm_function";
    public $timestamps = false;
    // protected $fillable = [
    //     'icon','code','app_code'

    // ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
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
