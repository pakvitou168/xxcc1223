<?php

namespace App\Traits;

use stdClass;

trait SMDataTable {
    /**
     *
     */
    public function generateTableData($query,callable $mapData = null) {
        $sorters = request()->sorters;
        if ($sorters) {
            $query->reorder();

            foreach ($sorters as $sorter) {
                $query->orderBy($sorter['field'], $sorter['dir']);
            }
        }

        $filters = request()->filters;

        // Filter
        $query = $query->where(function($q) use ($filters) {
            collect($filters)->filter(function($filter) {
                return collect($filter)->has(0);
            })->each(function($filter) use ($q) {
                collect($filter)->each(function($filter) use ($q) {
                    if ($filter['value'] != null) {
                        $filteredValue = $filter['type'] === 'like' ? '%'.$filter['value'].'%' : $filter['value'];
                        $q->where($filter['field'], $filter['type'], $filteredValue);
                    }
                });
            });
        });

        // Search
        $query = $query->where(function($q) use ($filters) {
            collect($filters)->filter(function($filter) {
                return !collect($filter)->has(0);
            })->each(function($filter) use ($q) {
                $relationColumns = explode('.', $filter['field']);
                $isRelationColumns = count($relationColumns) == 2;

                $condition = $filter['type'];
                $filteredValue = $filter['type'] === 'like' ? '%'.$filter['value'].'%' : $filter['value'];

                if (!$isRelationColumns) {
                    // Convert field to string to avoid conflict with lower()
                    $column = $filter['field'].'::text';
                    $q->orWhereRaw("lower($column) $condition lower('$filteredValue')");
                } else {
                    $relation = $relationColumns[0];
                    // Convert field to string to avoid conflict with lower()
                    $column = $relationColumns[1].'::text';

                    $q->orWhereHas($relation, function($q) use ($column, $condition, $filteredValue) {
                        $q->whereRaw("lower($column) $condition lower('$filteredValue')");
                    });
                }
            });
        });

        $count = $query->count();
        $size = request()->size;
        $last_page = $size ? ceil($count / $size) : 0;

        $page = request()->page ?? 1;
        $offset = ($page - 1) * $size;

        $query = $query->offset($offset)->limit($size);
        $data = $query->get()->map(function($item, $key) use($offset) {
            $item->no = $offset + $key + 1;
            return $item;
        });

        if($mapData !== null)
            $mapData($data);
        // Prepare data
        $response = new stdClass();
        $response->last_page = $last_page;
        $response->data = $data;
        $response->total = $count;
        $response->from = ($count > 0) ? (($page - 1) * $size) + 1 : 0;
        $response->to = ((($page - 1) * $size)) + $data->count();
        
        return $response;
    }
}
