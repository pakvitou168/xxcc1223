<?php

namespace App\Models\SecurityManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBranch extends Model
{
    use HasFactory;
    protected $table = "sm_user_branch";
    protected $fillable = ['user_id', 'branch_id'];
}
