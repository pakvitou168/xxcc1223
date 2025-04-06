<?php

namespace App\Repositories\SecurityManagement;

use App\Repositories\CrudRepository;
use App\Models\SecurityManagement\Application;

class ApplicationRepository extends CrudRepository implements ApplicationInterface {
  
  protected $model;

  public function __construct(Application $model) {
    parent::__construct($model);
    $this->model = $model;
  }

  public function allByStatusActive() {
    return $this->model
              ->active()
              ->orderBy('name', 'asc')
              ->get();
  }
}
