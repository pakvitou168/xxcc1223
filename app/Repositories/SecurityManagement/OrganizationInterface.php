<?php

namespace App\Repositories\SecurityManagement;

use App\Repositories\CrudInterface;

interface OrganizationInterface extends CrudInterface {
    public function allByStatusActive();
    public function allWithBranch();
}