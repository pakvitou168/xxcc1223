<?php
namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Str;

class DatatableHelper
{
    public static function getData($request, $query, $searchableCols = [], array $searchRemoval = [], $relationFilters = [], callable $advanceSearch = null)
    {

        $perPage = $request->per_page ?: 20;
        $data = $query;
        $searchQuery = null;

        if (!empty($request->search_query)) {
            $searchQuery = strip_tags(trim($request->search_query));

        }
        //search
        if ($searchQuery) {

            $rgx = array_merge($searchRemoval, ['s+']);
            foreach ($rgx as $i => $c) {
                $rgx[$i] = "\\" . $c;
            }
            $rgx = '/(' . join('|', $rgx) . ')/';

            $searchQuery = preg_split($rgx, $searchQuery, -1, PREG_SPLIT_NO_EMPTY);

            $data->where(function ($query) use ($searchableCols, $searchQuery, $advanceSearch) {
                $relationCols = [];
                foreach ($searchableCols as $index => $column) {
                    if (Str::of($column)->contains('.')) {
                        $relationCols[] = $column;
                        continue;
                    }
                    if ($index === 0) {
                        $query->where(function ($q) use ($searchQuery, $column) {
                            foreach ($searchQuery as $sq) {
                                $q->whereRaw("lower($column) like lower('%$sq%')");
                            }
                        });

                    } else {
                        $query->orWhere(function ($q) use ($searchQuery, $column) {
                            foreach ($searchQuery as $sq) {
                                $q->whereRaw("lower($column) like lower('%$sq%')");
                            }
                        });
                    }

                }

                if (is_callable($advanceSearch)) $advanceSearch($query);

                foreach ($relationCols as $rc) {
                    $relation = explode('.', $rc)[0];
                    $col = explode('.', $rc)[1];
                    $query->orWhereHas($relation, function ($query) use ($searchQuery, $col) {

                        foreach ($searchQuery as $sq) {
                            $query->whereRaw("lower($col) like lower('%$sq%')");
                        }

                    });
                }

            });
        }

        //Sort
        if ($request->sorter) {
            $sorter = json_decode($request->sorter);

            if (in_array(optional($sorter)->direction, ['desc', 'asc'])) {
                $data = $data->orderBy($sorter->field, $sorter->direction);
            }

        }

        //filter
        if ($filters = $request->filters) {

            $filters = json_decode($filters);

            foreach (get_object_vars($filters) as $key => $filter) {

                if ($filterModels = optional($filter)->constraints) {

                    //Relationship filter
                    if (array_key_exists($key, $relationFilters)) {
                        $data->whereHas($relationFilters[$key]['rel_name'], function ($query) use ($filter, $key, $filterModels, $relationFilters) {

                            foreach ($filterModels as $index => $filterModel) {

                                if (!$filterModel->value) {
                                    continue;
                                }

                                $filterQuery = self::getQuery($relationFilters[$key]['column'], $filterModel->matchMode, $filterModel->value);
                                if ($filterQuery) {
                                    if ('or' === $filter->operator) {
                                        if ($index === 0) {
                                            $query->whereRaw($filterQuery);
                                        } else {
                                            $query->orWhereRaw($filterQuery);
                                        }

                                    } else if ('and' === $filter->operator) {
                                        $query->whereRaw($filterQuery);
                                    }

                                }

                            }

                        });
                    } else {

                        $data->where(function ($query) use ($filter, $key, $filterModels) {
                            foreach ($filterModels as $index => $filterModel) {

                                if (!$filterModel->value) {
                                    continue;
                                }

                                $filterQuery = self::getQuery($key, $filterModel->matchMode, $filterModel->value);
                                if ($filterQuery) {
                                    if ('or' === $filter->operator) {
                                        if ($index === 0) {
                                            $query->whereRaw($filterQuery);
                                        } else {
                                            $query->orWhereRaw($filterQuery);
                                        }

                                    } else if ('and' === $filter->operator) {
                                        $query->whereRaw($filterQuery);
                                    }

                                }

                            }

                        });
                    }

                } else {
                    $filterModel = $filter;
                    if (!$filterModel->value) {
                        continue;
                    }

                    //Relationship filter
                    if (array_key_exists($key, $relationFilters)) {

                        $filterQuery = self::getQuery($relationFilters[$key]['column'], $filterModel->matchMode, $filterModel->value);
                        $data->whereHas($relationFilters[$key]['rel_name'], function ($query) use ($filterQuery) {

                            if ($filterQuery) {

                                $query->whereRaw($filterQuery);

                            }

                        });

                    } else {

                        $filterQuery = self::getQuery($key, $filterModel->matchMode, $filterModel->value);

                        if ($filterQuery) {

                            $data->whereRaw($filterQuery);

                        }
                    }
                }
            }

        }
        $data = $data->paginate($perPage);
        return $data;

    }

    private static function getQuery($column, $operator, $value)
    {
        switch ($operator) {
            case 'startsWith':
                return "$column like '$value%'";

            case 'endsWith':
                return "$column like '%$value'";

            case 'contains':
                return "$column like '%$value%'";

            case 'notContains':
                return "$column not like '%$value%'";

            case 'equals':
                return "$column = '$value'";

            case 'notEquals':
                return "$column <> '$value'";
            case 'in':

                return "$column in ('" . join("','", $value) . "')";

            case 'lt':
                return "$column < '$value'";
            case 'lte':
                return "$column <= '$value'";
            case 'gt':
                return "$column > '$value'";
            case 'gte':
                return "$column >= '$value'";
            case 'dateAfter':
                $date = Carbon::createFromFormat('Y-m-d', $value);
                return "$column > date '" . $date->toDateString() . "'";

            case 'dateBefore':
                $date = Carbon::createFromFormat('Y-m-d', $value);
                return "$column < date '" . $date->toDateString() . "'";
            case 'dateIs':
                $date = Carbon::createFromFormat('Y-m-d', $value); info($value); info($date->toDateString());
                return "$column >= date '" . $date->toDateString() . "' and $column < date '" . $date->addDay()->toDateString() . "'";
            case 'dateIsNot':
                $date = Carbon::createFromFormat('Y-m-d', $value);
                return "$column < date '" . $date->toDateString() . "' or $column >= date '" . $date->addDay()->toDateString() . "'";
                return null;

        }
    }
}
