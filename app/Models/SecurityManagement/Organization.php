<?php

namespace App\Models\SecurityManagement;

use App\Actionable;
 use App\Enums\RecordStatus;
use App\Traits\StatusScope;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use Actionable, StatusScope;

    protected $table = 'sm_org';
    protected $hidden = ['pivot'];

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

    public function branches() {
        return $this->hasMany(Branch::class, 'org_id');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'sm_user_org', 'org_id', 'user_id')->withTimestamps();
    }
}
