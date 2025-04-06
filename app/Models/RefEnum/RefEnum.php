<?php

namespace App\Models\RefEnum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefEnum extends Model
{
    use HasFactory;
    protected $table = 'ins_ref_enum';

    public static function idmActions()
    {
        return \App\Models\RefEnum::where('type_id', 'IDM_ACTION')->get();
    }
    public static function idmStatus()
    {
        return \App\Models\RefEnum::where('type_id', 'IDM_STATUS')->get()->pluck('name', 'enum_id');
    }
}
