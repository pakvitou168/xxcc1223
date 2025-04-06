<?php
namespace App\Helpers;


class ActionEventHelper
{
    public static function actions($request, $model) {
        $data = DatatableHelper::getData($request, $model->actions()->where('app_code', 'AXIS2')->with('target'));
        $data->getCollection()->transform(function($a) {
            $target = $a->target;
            $a->target_name = $target->{$target->actionKey ?: 'id'};
            unset($a->target);
            if($a->action_name === 'Attach') {

                $changes = [];
                foreach($a->changes as $k => $c) {
                    if(!in_array($k, ['id', 'status'])) {
                        $changes[$k] = $c;
                    }

                }
                $a->changes = $changes;
            }
            return $a;
        });

        return response([
            'success' => true,
            'data' => [
                'actions' => $data,
            ],
        ]);
    }
}
