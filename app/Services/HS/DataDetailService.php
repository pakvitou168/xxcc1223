<?php

namespace App\Services\HS;

use App\Models\HS\DataDetail;

class DataDetailService {
  public function __construct(protected DataDetail $model) {}

  public function create(array $data) {
    info('DataDetailService: create', $data);
    return $this->model->create($data);
  }
}