<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionEvent extends Model
{
    use HasFactory;
    protected $table = 'sys_audit_log';
    protected $connection = 'pgsql';
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'original' => 'array',
        'changes' => 'array',
        'created_at' => 'datetime'
    ];

    public function target() {
        return $this->morphTo();
    }
    
    public static function forResourceCreate($user, $model, $name, $requiresApproval = false)
    {
        return new static([
            
            'action_name' => $name,
            'actionable_type' => get_class($model),
            'actionable_id' => $model->getKey() ?: '',
            'target_type' => get_class($model),
            'target_id' => $model->getKey() ?: '',
            'model_type' => get_class($model),
            'model_id' => $model->getKey() ?: '',
            'original' => null,
            'changes' => $model->attributesToArray(),
            'status' => $requiresApproval ? 'pending' : 'finished',
            'exception' => '',
            'created_by' => $user->username,
            'app_code' => 'AXIS2',
            'ip_address' => request()->ip()
        ]);
    }
    public static function forResourceUpdate($user, $model, $name, $requiresApproval = false)
    {
        return new static([
            'action_name' => $name,
            'actionable_type' => get_class($model),
            'actionable_id' => $model->getKey(),
            'target_type' => get_class($model),
            'target_id' => $model->getKey(),
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'original' => array_intersect_key($model->getRawOriginal(), $model->getDirty()),
            'changes' => $model->getDirty(),
            'status' => $requiresApproval ? 'pending' : 'finished',
            'exception' => '',
            'created_by' => $user->username,
            'app_code' => 'AXIS2',
            'ip_address' => request()->ip()
        ]);
    }

    public static function forAttachedResource($request, $parent, $pivot, $requiresApproval = false)
    {
        return new static([
            'created_by' => $request->user()->username,
            'action_name' => 'Attach',
            'actionable_type' => get_class($parent),
            'actionable_id' => $parent->getKey(),
            'target_type' => get_class($parent->relatedModel),
            'target_id' => $parent->relatedModel->getKey(),
            'model_type' => get_class($pivot),
            'model_id' => $pivot->getKey(),
            'original' => null,
            'changes' => $pivot->attributesToArray(),
            'status' => $requiresApproval ? 'pending' : 'finished',
            'exception' => '',
            'app_code' => 'AXIS2',
            'ip_address' => request()->ip()
        ]);
    }

    public static function forResourceDetach($request, $parent, $pivotClass, $requiresApproval = false) {
        return new static([
            'created_by' => $request->user()->username,
            'action_name' => 'Detach',
            'actionable_type' => get_class($parent),
            'actionable_id' => $parent->getKey(),
            'target_type' => get_class($parent->relatedModel),
            'target_id' => $parent->relatedModel->getKey(),
            'model_type' => $pivotClass,
            'model_id' => null,
            'original' => null,
            'changes' => null,
            'status' => $requiresApproval ? 'pending' : 'finished',
            'exception' => '',
            'app_code' => 'AXIS2',
            'ip_address' => request()->ip()
        ]);
    }

    public static function forAttachedResourceUpdate($request, $parent, $pivot)
    {
        return new static([
            'created_by' => $request->user()->username,
            'action_name' => 'Update Attached',
            'actionable_type' => $parent->getMorphClass(),
            'actionable_id' => $parent->getKey(),
            'target_type' => get_class($parent->relatedModel),
            'target_id' => $parent->relatedModel->getKey(),
            'model_type' => get_class($pivot),
            'model_id' => $pivot->getKey(),
            'original' => array_intersect_key($pivot->getRawOriginal(), $pivot->getDirty()),
            'changes' => $pivot->getDirty(),
            'status' => 'finished',
            'exception' => '',
            'app_code' => 'AXIS2',
            'ip_address' => request()->ip()
        ]);
    }
}
