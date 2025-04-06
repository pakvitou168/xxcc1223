<?php

namespace App\Models\Deductible;

use App\Models\Cover\Cover;
use App\Models\Insurance\AutoDetail;
use Illuminate\Database\Eloquent\Model;

class DeductibleDetail extends Model
{
    protected $table = 'ins_prod_deductible_detail';

    public function cover() {
        return $this->belongsTo(Cover::class, 'comp_code', 'code');
    }

    public function autoDetail() {
        return $this->belongsTo(AutoDetail::class, 'detail_id', 'id');
    }

    public static function listByDetailAndProduct($detailId, $productCode) {
        return DeductibleDetail::with(['cover' => function($query) use ($productCode) {
            $query->select(
                'code',
                'name',
                'name_kh',
                'name_zh',
                'deductible_label',
                'deductible_label_kh',
                'deductible_label_zh',
                'seq',
            )
            ->where('product_code', $productCode)
            ->where('status', 'ACT');
        }])
            ->select('product_code', 'comp_code', 'value')
            ->where('detail_id', $detailId)
            ->get()
            ->sortBy('cover.seq')
            ->values();
    }
}
