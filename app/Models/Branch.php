<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $table = 'ins_branch';
    // protected $connection = 'oracle';

    // get branch list
    public static function getBranch()
    {
        $branches = Branch::select('code', 'name')->where('status', 'ACT')->get()->map(function ($item) {
            $item->label = $item->name;
            $item->value = $item->code;
            return collect($item)->only(['value', 'label']);
        });
        return $branches;
    }
}
