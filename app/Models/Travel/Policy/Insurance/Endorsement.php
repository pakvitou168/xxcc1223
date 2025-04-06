<?php

namespace App\Models\Travel\Policy\Insurance;

use App\Models\Travel\Policy\DataMaster;
use App\Models\Travel\Policy\Policy;
use App\Models\UserPermissionTrait;

class Endorsement extends Policy
{
    use UserPermissionTrait;

    static $functionCode = 'ENDORSEMENT';

    public function travel()
    {
        return $this->belongsTo(DataMaster::class, 'data_id', 'id');
    }

    public function endorsementView()
    {
        return $this->belongsTo(EndorsementView::class,'id','id');
    }
}
