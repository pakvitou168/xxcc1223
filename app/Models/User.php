<?php

namespace App\Models;

use App\Models\UserManagement\Functions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Plb\SecurityManagement\Models\User as ModelsUser;
class User extends ModelsUser
{
}
