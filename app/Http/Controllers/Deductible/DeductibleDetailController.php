<?php

namespace App\Http\Controllers\Deductible;

use App\Http\Controllers\Controller;
use App\Models\Deductible\DeductibleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeductibleDetailController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Deductible\DeductibleDetail  $deductibleDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeductibleDetail $deductibleDetail)
    {
        $deductibleDetail->cond_value = $request->cond_value;
        $deductibleDetail->min_value = $request->min_value;
        $deductibleDetail->value = $this->getUpdatedValue(
            $deductibleDetail->value_label, 
            $request->cond_value,
            $request->min_value,
            $deductibleDetail->cond_value_type
        );

        if ($deductibleDetail->save()) {

            return response([
                'success' => true,
                'message' => 'Updated successfully!'
            ]);
        }
    }

    private function getUpdatedValue($label, $value, $minValue, $valueType) {
        if ($valueType === 'AMOUNT') {
            // Get only first word as label if value type is AMOUNT
            return explode(' ', $label)[0] . ' ' . number_format($value, 2);
        }
        if ($valueType === 'PERCENTAGE') return $value . $label . ($minValue > 0 ? number_format($minValue, 2) : '');

        Log::error('Deductible detail: Wrong value type');
        return '';
    }

    /**
     * Update multiple resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updates(Request $request) {
        $formData = collect($request);

        DB::beginTransaction();

        try {
            $formData->each(function($vehicle) {
                collect($vehicle)->each(function($item) {
                    $deductibleDetail = DeductibleDetail::find($item['id']);
    
                    if ($deductibleDetail)
                        $this->update(new Request($item), $deductibleDetail);
                });
            });

            DB::commit();

            return [
                'success' => true,
                'message' => 'Deductible is updated'
            ];
        } catch(\Throwable $ex) {
            DB::rollBack();
            Log::error('Updating deductible detail error: ' . $ex->getMessage());
        }
    }
}
