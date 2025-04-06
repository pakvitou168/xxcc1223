<?php

namespace App\Services\HS;

use App\Models\HS\PlanData;

class PlanDataService {
  public function __construct(protected PlanData $model) {}

  public function create(array $data) {
    info('PlanDataService: create', $data);
    return $this->model->create($data);
  }
}