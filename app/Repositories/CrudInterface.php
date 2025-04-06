<?php

namespace App\Repositories;

use Illuminate\Http\Request;

interface CrudInterface {
  public function all();
  public function find($id);
  public function create(array $data);
  public function update(array $data, $id);
  public function delete($id);
  public function query();
  public function datatable(Request $request, array|object $query = [], array $searchable = []);
}
