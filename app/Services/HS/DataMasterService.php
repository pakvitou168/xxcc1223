<?php

namespace App\Services\HS;

use App\Models\HS\DataMaster;

class DataMasterService {
  public function __construct(protected DataMaster $model) {}

  public function create(array $data) {
    info('DataMasterService: create', $data);
    return $this->model->create($data);
  }

  public function update(array $data, $id) {
    info('DataMasterService: update', $data);

    return $this->model->find($id)->update($data);
  }
}