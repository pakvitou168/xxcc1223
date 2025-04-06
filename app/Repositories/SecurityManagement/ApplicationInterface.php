<?php

namespace App\Repositories\SecurityManagement;

use App\Repositories\CrudInterface;

interface ApplicationInterface extends CrudInterface {
    public function allByStatusActive();
}