<?php

namespace App\Http\Controllers\ProductConfiguration\CoverComponent;

use App\Http\Controllers\Controller;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use App\Models\CoverComponent\CoverComponent;

class CoverComponentController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(CoverComponent::class, 'cover_component');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(CoverComponent::with('product:code,name')
                                                        ->where('status','!=','DEL')
                                                        ->where('type', 'R')
                                                        ->latest('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        $coverComponent = new CoverComponent();

        $this->assignValues($request, $coverComponent);

        if ($coverComponent->save())
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
    }

    private function validateRequest($request) {
        $request->validate([
            'product_code' => 'required',
            'code' => 'required|max:50',
            'name' => 'required|max:50',
            'value' => 'required'
        ]);
    }

    private function assignValues($request, $coverComponent) {
        $coverComponent->product_code = $request->product_code;
        $coverComponent->code = $request->code;
        $coverComponent->name = $request->name;
        $coverComponent->name_kh = $request->name_kh;
        $coverComponent->name_zh = $request->name_zh;
        $coverComponent->description = $request->description;
        $coverComponent->description_kh = $request->description_kh;
        $coverComponent->description_zh = $request->description_zh;
        $coverComponent->mandatory = $request->mandatory;
        $coverComponent->value = $request->value;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CoverComponent\CoverComponent  $coverComponent
     * @return \Illuminate\Http\Response
     */
    public function show(CoverComponent $coverComponent)
    {
        $coverComponent->load('product');
        return $coverComponent;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CoverComponent\CoverComponent  $coverComponent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CoverComponent $coverComponent)
    {
        $this->validateRequest($request);

        $this->assignValues($request, $coverComponent);

        if ($coverComponent->save())
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CoverComponent\CoverComponent  $coverComponent
     * @return \Illuminate\Http\Response
     */
    public function destroy(CoverComponent $coverComponent)
    {
        $coverComponent->status = "DEL";
        
        if ($coverComponent->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
