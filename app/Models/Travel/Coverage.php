<?php

namespace App\Models\Travel;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Coverage extends Model
{
    protected $table = 'ins_tv_coverage';
    protected $appends = [
        'name_km'
    ];

    public function translation()
    {
        return $this->hasMany(CoverageDataV::class, 'data_id', 'id');
    }

    public function nameKm(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->translation()->whereLangCode('KM')->value('name')
        );
    }
}
