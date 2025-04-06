<?php

namespace App\Repositories\SecurityManagement;

use App\Models\SecurityManagement\Fnc;
use App\Repositories\CrudRepository;
class FunctionRepository extends CrudRepository implements FunctionInterface {

    protected $model;

    public function __construct(Fnc $model) {
        parent::__construct($model);
        $this->model = $model;
    }
}
