<?php

namespace App\Traits;

trait StatusScope
{
    public function scopeActive($query)
    {
        $query->where('status', 'ACT');
    }

    public function scopeNotDeleted($query)
    {
        $query->where('status', '<>', 'DEL');
    }
}
