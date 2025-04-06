<?php

namespace App\Models\HS\Clinic;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $table = 'ins_hs_clinic';
    protected $fillable = ['name', 'type', 'status', 'contact_name', 'contact_number', 'latitude', 'longitude', 'address','created_by','updated_by'];

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
