<?php

namespace App\Models\Travel;

use Illuminate\Database\Eloquent\Model;

class DeductibleData extends Model
{
    protected $table = 'ins_tv_deductible_data';

    // public function cover() {
    //     return $this->belongsTo(Cover::class, 'comp_code', 'code');
    // }

    // public function autoDetail() {
    //     return $this->belongsTo(AutoDetail::class, 'detail_id', 'id');
    // }

    // public static function listByDetailAndProduct($detailId, $productCode) {
    //     return DeductibleDetail::with(['cover' => function($query) use ($productCode) {
    //         $query->select(
    //             'code',
    //             'name',
    //             'name_kh',
    //             'name_zh',
    //             'deductible_label',
    //             'deductible_label_kh',
    //             'deductible_label_zh',
    //             'seq',
    //         )
    //         ->where('product_code', $productCode)
    //         ->where('status', 'ACT');
    //     }])
    //         ->select('product_code', 'comp_code', 'value')
    //         ->where('detail_id', $detailId)
    //         ->get()
    //         ->sortBy('cover.seq')
    //         ->values();
    // }
}
