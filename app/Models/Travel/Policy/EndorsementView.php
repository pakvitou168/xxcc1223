<?php

namespace App\Models\Travel\Policy;

use App\Models\Product\Product;
use App\Models\UserManagement\User\User;
use App\Scopes\EndorsementScope;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class EndorsementView extends Model
{
    protected $table = 'ins_tv_policy_v';
    protected $appends = ['origin_status'];

    protected static function booted()
    {
        // Scope for policy with no version (endorsement)
        static::addGlobalScope(new EndorsementScope);
    }

    public function insuredPersons()
    {
        return $this->hasMany(InsuredPersonV::class, 'data_id');
    }

    public function getOriginStatusAttribute()
    {
        return $this->status;
    }

    protected function endorsementType(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => str_replace(' ', '', $value)
        );
    }

    public function getCreatedByAttribute()
    {
        return $this->endorsement->created_by;
    }

    public function getUpdatedByAttribute()
    {
        return $this->endorsement->updated_by;
    }

    public function issuedByName($userId)
    {
        return User::findOr($userId, fn() => throw new Exception("User not found"))?->username;
    }

    public function endorsement()
    {
        return $this->belongsTo(Policy::class, 'id', 'id');
    }

    public function travel()
    {
        return $this->belongsTo(DataMaster::class, 'data_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }
    public function dataDetails()
    {
        return $this->hasMany(DataDetail::class, 'master_data_id', 'data_id');
    }
}
