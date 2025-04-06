<?php

namespace App\Models\HS\Insurance;

use App\Models\HS\DataMaster;
use App\Models\HS\Policy;
use App\Models\UserPermissionTrait;

class Endorsement extends Policy
{
    use UserPermissionTrait;

    static $functionCode = 'ENDORSEMENT';

    public function hs()
    {
        return $this->belongsTo(DataMaster::class, 'data_id', 'id');
    }

    public function endorsementView()
    {
        return $this->belongsTo(EndorsementView::class,'id','id');
    }
}
