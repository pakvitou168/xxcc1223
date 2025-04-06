<?php

namespace App\Http\Controllers\BusinessManagement;

use App\Http\Controllers\Controller;
use App\Models\BusinessManagement\BusinessCategory;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BusinessCategoryController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(BusinessCategory::class, 'business_category');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(BusinessCategory::where('status', 'ACT')->orderByDesc('id'));
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

        $businessCategory = new BusinessCategory();
        $this->assignValues($request, $businessCategory);

        if ($businessCategory->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request, $businessCategory) {
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'max:500',
            'prefix' => [
                'required',
                'max:10',
                Rule::unique(BusinessCategory::class, 'prefix')->ignore($businessCategory)->where('status', 'ACT')
            ],
        ]);
    }

    private function assignValues($request, $businessCategory) {
        $businessCategory->name = $request->post('name');
        $businessCategory->description = $request->post('description');
        $businessCategory->prefix = $request->post('prefix');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusinessManagement\BusinessCategory  $businessCategory
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessCategory $businessCategory)
    {
        return $businessCategory;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessManagement\BusinessCategory  $businessCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessCategory $businessCategory)
    {
        return $businessCategory;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessManagement\BusinessCategory  $businessCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusinessCategory $businessCategory)
    {
        $this->validateRequest($request, $businessCategory);

        $this->assignValues($request, $businessCategory);

        if ($businessCategory->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessManagement\BusinessCategory  $businessCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessCategory $businessCategory)
    {
        $businessCategory->status = 'DEL';

        if ($businessCategory->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
