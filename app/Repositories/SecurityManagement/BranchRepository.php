<?php

namespace App\Repositories\SecurityManagement;

use App\Repositories\CrudRepository;
use App\Models\SecurityManagement\Branch;
use App\Models\SecurityManagement\Organization;

class BranchRepository extends CrudRepository implements BranchInterface {

    protected $model;

    public function __construct(Branch $model) {
        parent::__construct($model);
        $this->model = $model;
    }

    public function listBranchesByOrg($org_code) {
        $org = Organization::where('code', $org_code ?? 'PLB')->first();
        return $this->model->where('org_id', $org->id)
                            ->active()
                            ->get();
    }
}
