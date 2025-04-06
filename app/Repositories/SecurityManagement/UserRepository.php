<?php

namespace App\Repositories\SecurityManagement;

use App\Models\SecurityManagement\User;
use App\Repositories\CrudRepository;
class UserRepository extends CrudRepository implements UserInterface {

    protected $model;

    public function __construct(User $model) {
        parent::__construct($model);
        $this->model = $model;
    }

    public function findWithRelationship($id) {
        return $this->model->with([
                    'organizations:id,name',
                    'branches:id,name,org_id',
                    'groups:id,name',
                    'roles:id,name',
                    'permissions:id,name',
                ])->find($id);
    }
}
