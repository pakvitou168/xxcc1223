<?php

namespace App\Repositories;

use App\Enums\RecordStatus;
use App\Helpers\DatatableHelper;
use Illuminate\Http\Request;

abstract class CrudRepository implements CrudInterface
{
  public function __construct(protected $model) {}

  public function all() {
    return $this->model->orderBy('id', 'desc')->get();
  }

  public function find($id) {
    return $this->model->find($id);
  }

  public function create(array $data) {
    return $this->model->create($data);
  }

  public function update(array $data, $id) {
    return $this->model->find($id)->update($data);
  }

  public function delete($id) {
    return $this->update(['status' => RecordStatus::DELETE->value], $id);
  }

  public function query() {
    return $this->model->query();
  }

  public function datatable(Request $request, array|object $query = [], array $searchable = []) {
    if (!$query) {
      $query = $this->model;
    }

    $sorter = json_decode($request->sorter);
    if (!in_array(optional($sorter)->direction, ['desc', 'asc']))
      $query = $query->orderBy('id', 'desc');

    $filters = json_decode($request->filters);
    if (!(optional(optional($filters)->status)->value != null && in_array(RecordStatus::DELETE->value, optional(optional($filters)->status)->value)))
      $query = $query->where('status', '!=', RecordStatus::DELETE->value);

    return DatatableHelper::getData($request, $query, $searchable);
  }
}
