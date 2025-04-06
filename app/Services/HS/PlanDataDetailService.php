<?php

namespace App\Services\HS;

use App\Models\HS\PlanDataDetail;

class PlanDataDetailService {
  public function __construct(protected PlanDataDetail $model) {}

  public function create(array $data) {
    info('PlanDataDetailService: create', $data);
    return $this->model->create($data);
  }
}