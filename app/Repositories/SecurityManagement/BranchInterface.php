<?php

namespace App\Repositories\SecurityManagement;

use App\Repositories\CrudInterface;

interface BranchInterface extends CrudInterface {
    public function listBranchesByOrg($org_code);
}