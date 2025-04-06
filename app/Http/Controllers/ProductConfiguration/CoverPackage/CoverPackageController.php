<?php

namespace App\Http\Controllers\ProductConfiguration\CoverPackage;

use App\Http\Controllers\Controller;
use App\Models\CoverPackage\CoverPackage;
use App\Traits\DataTable;
use Illuminate\Http\Request;

class CoverPackageController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(CoverPackage::class, 'cover_package');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return $this->generateTableData(
            CoverPackage::with(
                ['product' => function($query) {
                    $query->select('code', 'name');
                }]
            )
            ->with(
                ['coverPackageComponents' => function($query) {
                    $query->select('cpkg_id', 'comp_code');
                }]
            )
            ->where('status', 'ACT')->orderByDesc('id'));
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

        $coverPackage = new CoverPackage();
        $this->assignValues($request, $coverPackage);

        if ($coverPackage->save())
            if ($this->storeCoverPackageComponents($request, $coverPackage))
                return response([
                    'success' => true,
                    'message' => 'Record is created.'
                ], 201);
    }

    private function validateRequest($request) {
        $request->validate([
            'product_code' => 'required',
            'name' => 'required|max:50',
            'description' => 'max:1000'
        ]);
    }

    private function assignValues($request, $model) {
        $model->product_code = $request->post('product_code');
        $model->name = $request->post('name');
        $model->description = $request->post('description');
    }

    private function storeCoverPackageComponents($request, $coverPackage) {
        $covers = $request->post('cover_package_components');

        $coverPackageComponents = collect($covers)->map(function($item) use ($coverPackage) {
            return [
                'product_code' => $coverPackage->product_code,
                'cpkg_id' => $coverPackage->id,
                'comp_code' => $item
            ];
        });

        $coverPackage->coverPackageComponents()->delete();
        return $coverPackage->coverPackageComponents()->createMany($coverPackageComponents);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CoverPackage\CoverPackage  $coverPackage
     * @return \Illuminate\Http\Response
     */
    public function show(CoverPackage $coverPackage)
    {
        // existing covers
        $coverPackage->cover_package_components = $coverPackage->coverPackageComponents()
            ->select('comp_code')
            ->get()
            ->pluck('comp_code');

        $coverPackage->product = $coverPackage->product()
            ->select('name')
            ->first();
        
        return $coverPackage;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CoverPackage\CoverPackage  $coverPackage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CoverPackage $coverPackage)
    {
        $this->validateRequest($request);

        $this->assignValues($request, $coverPackage);

        if ($coverPackage->save())
            if ($this->storeCoverPackageComponents($request, $coverPackage))
                return [
                    'success' => true,
                    'message' => 'Record is updated.'
                ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CoverPackage\CoverPackage  $coverPackage
     * @return \Illuminate\Http\Response
     */
    public function destroy(CoverPackage $coverPackage)
    {
        $coverPackage->status = "DEL";
        
        if ($coverPackage->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
