<?php

namespace App\Models\SecurityManagement;

use App\Actionable;
 use App\Enums\RecordStatus;
use App\Traits\StatusScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\SecurityManagement\Organization;
use Plb\SecurityManagement\Models\Branch as SmBranch;
class Branch extends SmBranch
{
    use Actionable, StatusScope;

    protected $table = 'sm_branch';
    protected $hidden = ['pivot'];

    protected $fillable = [
        'code',
        'name',
        'org_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'status' => RecordStatus::class
    ];

    public function organization() {
        return $this->belongsTo(Organization::class, 'org_id');
    }
    
    public function users() {
        return $this->belongsToMany(Branch::class, 'sm_user_branch', 'branch_id', 'user_id')->withTimestamps();
    }
}
