<?php

namespace App\Repositories\SecurityManagement;

use App\Repositories\CrudInterface;

interface GroupInterface extends CrudInterface {
    public function findWithRole($id);
    public function allByStatusActive();
}