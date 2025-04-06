<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordStatus extends Model
{
    const ACTIVE = 'ACT';
    const DELETED = 'DEL';
    const PENDING = 'PND';
    const APPROVED = 'APV';
    const REJECTED = 'REJ';
    const ACCEPTED = 'ACP';
    const SUBMITTED = 'SBM';
    const REVISED = 'REV';

    public static function approvalActions(){
        return [
            self::APPROVED,
            self::REJECTED,
            self::ACCEPTED
        ];
    }
}
