<?php

namespace App\Traits;

use stdClass;

trait DataTable {
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
            $item->actions = $this->actionButtons($item, $item->userPermissions ?? []);
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
        
        return response()->json($response);
    }

    /**
     *
     */
    public function actionButtons($item, $permissions) {
        if ($permissions) {
            $canView = $permissions['VIEW'];
            $canUpdate = $permissions['UPDATE'];
            $canDelete = $permissions['DELETE'];
        } else {
            $canView = $canUpdate = $canDelete = true;
        }

        $showViewButton = $canView ? '' : 'hidden';
        $showUpdateButton = $canUpdate ? '' : 'hidden';
        $showDeleteButton = $canDelete ? '' : 'hidden';

        return '
            <div class="flex justify-center">
                <a class="view flex items-center mr-1 text-sm '.$showViewButton.'" href="javascript:;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </a>
                <a class="edit flex items-center mr-1 '.$showUpdateButton.'" href="javascript:;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </a>
                <a class="delete flex items-center text-theme-6 '.$showDeleteButton.'" href="javascript:;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </a>
            </div>
        ';
    }
}
