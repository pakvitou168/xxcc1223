<?php

namespace App\Models\Travel;

use Illuminate\Database\Eloquent\Model;

class DataDetailV extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ins_tv_dd_v';

    public function dataMaster()
    {
        return $this->belongsTo(DataMaster::class, 'data_id');
    }
}