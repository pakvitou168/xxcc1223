<?php

namespace App\Policies\ProductConfiguration;

use App\Policies\PolicyTrait;

class VehicleUsagePolicy
{
    use PolicyTrait;

    static $functionCode = 'VEHICLE_USAGE';
}
