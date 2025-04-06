<?php

namespace App\Http\Controllers\Cover;

use App\Http\Controllers\Controller;
use App\Models\Cover\Cover;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CoverController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Cover::class, 'cover');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            Cover::with('product:code,name')
                ->where('type', 'C')
                ->where('status', 'ACT')
                ->latest('id')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request, null);

        $cover = new Cover();
        $this->assignValues($request, $cover);

        if ($cover->save())
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
    }

    private function validateRequest($request, $cover) {
        $request->validate([
            'product_code' => 'required',
            'code' => [
                'required',
                'max:5',
                Rule::unique(Cover::class, 'code')
                    ->ignore($cover)
                    ->where('product_code', $request->product_code)
                    ->where('status', 'ACT')
            ],
            'name' => 'required|max:50',
            'deductible_label' => 'max:250'
        ]);
    }

    private function assignValues($request, $cover) {
        $cover->product_code = $request->product_code;
        $cover->code = $request->code;
        $cover->name = $request->name;
        $cover->name_kh = $request->name_kh;
        $cover->name_zh = $request->name_zh;
        $cover->description = $request->description;
        $cover->description_kh = $request->description_kh;
        $cover->description_zh = $request->description_zh;
        $cover->detail = $request->detail;
        $cover->detail_kh = $request->detail_kh;
        $cover->detail_zh = $request->detail_zh;
        $cover->mandatory = $request->mandatory;
        $cover->value = $request->value;
        $cover->seq = $request->seq;
        $cover->deductible_label = $request->deductible_label;
        $cover->deductible_label_kh = $request->deductible_label_kh;
        $cover->deductible_label_zh = $request->deductible_label_zh;
        $cover->is_required_vehicle_val = $request->is_required_vehicle_val;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cover\Cover  $cover
     * @return \Illuminate\Http\Response
     */
    public function show(Cover $cover)
    {
        $cover->load('product');
        $cover->html_detail = nl2br($cover->detail);
        $cover->html_detail_kh = nl2br($cover->detail_kh);
        $cover->html_detail_zh = nl2br($cover->detail_zh);

        return $cover;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cover\Cover  $cover
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cover $cover)
    {
        $this->validateRequest($request, $cover);

        $this->assignValues($request, $cover);

        if ($cover->save())
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cover\Cover  $cover
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cover $cover)
    {
        $cover->status = "DEL";

        if ($cover->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
