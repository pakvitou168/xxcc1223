<?php

namespace App;

use App\Models\ActionEvent;
use Illuminate\Support\Facades\DB;

trait Actionable
{

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::updating(function ($model) {
            (new ActionEvent)->forResourceUpdate(request()->user(), $model, 'Update')->save();
        });
        static::created(function ($model) {
            (new ActionEvent)->forResourceCreate(request()->user(), $model, 'Create')->save();
        });
    }

    function actions()
    {
        // return $this->hasMany(ActionEvent::class, 'actionable_id', $this->primaryKey);
        return $this->morphMany(ActionEvent::class, 'actionable');
    }

    public function syncWithEvent(string $relationName, array $data, array $wherePivot = []): void
    {
        DB::transaction(function () use ($relationName, $data, $wherePivot) {

            $relatedModel = $this->{$relationName}()->getRelated();
            $relatedQuery = $this->{$relationName}()->select($relatedModel->getTable() . '.' . $relatedModel->getKeyName());
            foreach ($wherePivot as $key => $value) {
                $relatedQuery->wherePivot($key, $value);
            }
            
            $relatedModels = $relatedQuery->get();
            foreach ($data as $rid => $pivot) {
                if ($existingRelation = $relatedModels->where($relatedModel->getKeyName(), $rid)->first()) {
                    //Update attached
                    $existingPivot = $existingRelation->pivot;
                    $pivotData = [];
                    foreach ($pivot as $key => $value) {
                        if (array_key_exists($key, $existingPivot->toArray())) {
                            $pivotData[$key] = $value;
                        }
                    }
                    $existingPivot->fill($pivotData);
                    $existingPivot->save();
                    
                } else {
                    //Attach
                    $this->{$relationName}()->attach([$rid => $pivot]);
                }
            }
            //Detach
            foreach ($relatedModels as $rm) {
                if (!array_key_exists($rm->{$relatedModel->getKeyName()}, $data)) {
                    $rm->pivot->delete();
                }
            }
        });
    }

    /* function action() {
        return $this->morphOne(ActionEvent::class);
    } */
}
