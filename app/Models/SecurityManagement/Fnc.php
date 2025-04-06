<?php

namespace App\Models\SecurityManagement;

use App\Actionable;
use App\Enums\RecordStatus;
use App\Traits\StatusScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
class Fnc extends Model
{
    use Actionable, StatusScope;

    protected $table = 'sm_function';
    protected $appends = [
        'app_name'
    ];
    protected $fillable = [
        'code',
        'name',
        'status',
        'app_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'status' => RecordStatus::class
    ];

    protected function appName(): Attribute
    {
        return Attribute::make(
            get: fn($value) => @$this->app->name
        );
    }
    public function permission()
    {
        return $this->hasMany(Permission::class);
    }
    public function app()
    {
        return $this->belongsTo(Application::class, 'app_id');
    }
}
