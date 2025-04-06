<?php

namespace App\Repositories\Function;

use App\Models\UserManagement\Functions AS Fnc;

class SmFncRepository extends Fnc implements FncRepositoryContract
{
    protected $connection = 'pgsql';
    protected $table = 'sm_function';

}
