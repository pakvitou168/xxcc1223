<?php

namespace App\Http\Controllers\UserManagement\Functions;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Functions;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FunctionController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Functions::class, 'function');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(Functions::latest('id'));
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

        $function = new Functions();

        $this->assignValues($request, $function);

        if ($function->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request, $model) {
        $request->validate([
            'code' => [
                'required',
                'max:50',
                Rule::unique(Functions::class, 'code')->ignore($model)->where('status', 'ACT')
            ],
            'name' => 'required|max:100',
            'description' => 'max:250'
        ]);
    }

    private function assignValues($request, $model) {

        $appCode = 'PGI';

        $model->code = $request->post('code');
        $model->name = $request->post('name');
        $model->app_code = $appCode;
        $model->description = $request->post('description');
        $model->permission = collect($request->post('permission'))->join('#');
        $model->status = $request->post('status');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserManagement\Functions  $function
     * @return \Illuminate\Http\Response
     */
    public function show(Functions $function)
    {
        $function->permission = explode('#', $function->permission);

        return $function;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserManagement\Functions  $function
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Functions $function)
    {
        $this->validateRequest($request, $function);

        $this->assignValues($request, $function);

        if ($function->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserManagement\Functions  $function
     * @return \Illuminate\Http\Response
     */
    public function destroy(Functions $function)
    {
        $function->status = "DEL";

        if ($function->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
