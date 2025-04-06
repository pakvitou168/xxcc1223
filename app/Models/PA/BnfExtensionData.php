<?php

namespace App\Models\PA;

use App\Models\RecordStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class BnfExtensionData extends Model
{
    protected $table = 'ins_pa_extension_selection';
    protected $fillable = [
        'status',
        'data_id',
        'extension_id',
        'extension_code',
        'extension_name',
        'extension_description',
        'is_selected',
        'amount_type',
        'rating'
    ];
    public function benefit()
    {
        return $this->belongsTo(BnfExtension::class, 'extension_id');
    }
    protected function rating(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value * 100,
            set: fn($value) => round($value / 100, 7)
        );
    }
    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = RecordStatus::ACTIVE;
            $obj->created_by = auth()->id();
        });
        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }
}
