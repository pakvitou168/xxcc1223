<?php

namespace App\Models\SecurityManagement;

use App\Actionable;
use App\Enums\RecordStatus;
use App\Traits\StatusScope;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use Actionable, StatusScope;

    protected $table = 'sm_app';

    protected $fillable = [
        'name',
        'code',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'status' => RecordStatus::class,
    ];

    public function permissions() {
        return $this->hasMany(Permission::class, 'app_id', 'id');
    }
}
