<?php
namespace App\Traits;

use App\Models\ActionEvent;

trait Actionable {
    
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::updating(function ($model) {
            $eventName = $model->action_event_name ?? 'Update';
            unset($model->action_event_name);
            (new ActionEvent)->forResourceUpdate(request()->user(), $model, $eventName)->save();
        });
        static::created(function ($model) {
            (new ActionEvent)->forResourceCreate(request()->user(), $model, 'Create')->save();
        });
    }

    /**
     * Get all of the action events for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function actions()
    {
        return $this->morphMany(ActionEvent::class, 'actionable');
    }

    /* function actions() {
        return $this->hasMany(ActionEvent::class, 'actionable_id', $this->primaryKey);
    } */

    /* function action() {
        return $this->morphOne(ActionEvent::class);
    } */
}