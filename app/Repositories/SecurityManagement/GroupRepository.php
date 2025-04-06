<?php

namespace App\Repositories\SecurityManagement;

use App\Repositories\CrudRepository;
use App\Models\SecurityManagement\Group;

class GroupRepository extends CrudRepository implements GroupInterface {

    protected $model;

    public function __construct(Group $model) {
        parent::__construct($model);
        $this->model = $model;
    }

    public function findWithRole($id) {
        return $this->model->with('roles:id,name')->find($id);
    }

    public function allByStatusActive() {
        return $this->model
                    ->active()
                    ->orderBy('name', 'asc')
                    ->get();
      }
}
