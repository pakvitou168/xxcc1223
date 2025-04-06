<?php

namespace App\Models\PA;

use Illuminate\Database\Eloquent\Model;

class WorkingClass extends Model
{
    protected $table = 'ins_pa_working_class';
    public static function codeList()
    {
        return self::pluck('code')->toArray();
    }
    public static function selection()
    {
        return self::select('name AS label', 'code AS value')->get()->toArray();
    }
}
