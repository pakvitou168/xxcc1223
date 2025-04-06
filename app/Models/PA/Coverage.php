<?php

namespace App\Models\PA;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Coverage extends Model
{
    protected $table = 'ins_pa_coverage_type';
    protected $appends = [
        'name_km'
    ];

    public function translation()
    {
        return $this->hasMany(CoverageV::class, 'coverage_type_id', 'id');
    }
    public function nameKm(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->translation()->whereLangCode('KM')->value('name')
        );
    }
}
