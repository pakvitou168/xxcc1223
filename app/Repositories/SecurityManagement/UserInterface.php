<?php

namespace App\Repositories\SecurityManagement;

use App\Repositories\CrudInterface;

interface UserInterface extends CrudInterface {
    public function findWithRelationship($id);
}