<?php

namespace App\Models;

trait UserPermissionTrait
{
    public function getUserPermissionsAttribute()
    {
        return [
            'VIEW' => auth()->user()->can('view', $this),
            'UPDATE' => auth()->user()->can('update', $this),
            'DELETE' => auth()->user()->can('delete', $this),
            'UPLOAD' => auth()->user()->can('upload', $this)
        ];
    }
}
