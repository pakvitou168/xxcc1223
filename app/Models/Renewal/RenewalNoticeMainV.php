<?php

namespace App\Models\Renewal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserManagement\User\User;

class RenewalNoticeMainV extends Model
{
    use HasFactory;
    protected $table = 'ins_renewal_notice_main_v';

    public function issuedByName($issued_by_id) {
        return User::where('id', $issued_by_id)->value('full_name');
    }
}
