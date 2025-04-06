<?php

namespace App\Http\Controllers\ProductConfiguration\ProductLine;

use App\Http\Controllers\Controller;
use App\Models\ProductLine\ProductLine;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductLineController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(ProductLine::class, 'product_line');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return $this->generateTableData(ProductLine::where('status', 'ACT')->orderByDesc('id'));
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

        $productLine = new ProductLine();
        $this->assignValues($request, $productLine);

        if ($productLine->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request, $productLine) {
        $request->validate([
            'code' => [
                'required',
                'max:25',
                Rule::unique(ProductLine::class, 'code')->ignore($productLine)->where('status', 'ACT')
            ],
        ]);
    }

    private function assignValues($request, $productLine) {
        $productLine->code = $request->post('code');
        $productLine->description = $request->post('description');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductLine\ProductLine  $productLine
     * @return \Illuminate\Http\Response
     */
    public function show(ProductLine $productLine)
    {
        return $productLine;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductLine\ProductLine  $productLine
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductLine $productLine)
    {
        return $productLine;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductLine\ProductLine  $productLine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductLine $productLine)
    {
        $this->validateRequest($request, $productLine);

        $this->assignValues($request, $productLine);

        if ($productLine->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductLine\ProductLine  $productLine
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductLine $productLine)
    {
        $productLine->status = "DEL";

        if ($productLine->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
