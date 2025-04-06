<?php

namespace App\Repositories\SecurityManagement;

use App\Models\SecurityManagement\Organization;
use App\Repositories\CrudRepository;
class OrganizationRepository extends CrudRepository implements OrganizationInterface {
  
  protected $model;

  public function __construct(Organization $model) {
    parent::__construct($model);
    $this->model = $model;
  }

  public function allByStatusActive() {
    return $this->model
                ->active()
                ->orderBy('name', 'asc')
                ->get();
  }

  public function allWithBranch() {
    return $this->model->with(['branches' => function($query){
                    $query->select('id', 'org_id', 'name')
                          ->active()
                          ->orderBy('name', 'asc');
                }])
                ->active()
                ->orderBy('name', 'asc')
                ->get();
  }
}
