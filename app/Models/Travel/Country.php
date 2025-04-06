<?php

namespace App\Models\Travel;

use App\Traits\StatusScope;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use StatusScope;
    protected $table = 'ins_tv_country';
}
