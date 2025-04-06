<?php

namespace App\Services\HS;

use App\Models\HS\SchemaData;

class SchemaDataService {
  public function __construct(protected SchemaData $model) {}

  public function create(array $data) {
    info('SchemaDataService: create', $data);
    return $this->model->create($data);
  }
  public function getNumberOfInsuredPerson($masterDataId) {
    return $this->model->where('master_data_id', $masterDataId)
      ->where('key', 'TOTAL')
      ->where('schema_type', 'STANDARD')
      ->value('no_person');
  }
}