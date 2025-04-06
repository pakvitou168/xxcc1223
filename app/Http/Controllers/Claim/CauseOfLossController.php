<?php

namespace App\Http\Controllers\Claim;

use App\Http\Controllers\Controller;
use App\Models\Claim\CauseOfLoss;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CauseOfLossController extends Controller
{
    use DataTable;

    public function __construct() {
        $this->middleware('has-permission:CAUSE_OF_LOSE.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:CAUSE_OF_LOSE.NEW')->only('store');
        $this->middleware('has-permission:CAUSE_OF_LOSE.UPDATE')->only('update');
        $this->middleware('has-permission:CAUSE_OF_LOSE.DELETE')->only('destroy');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            CauseOfLoss::with(['product' => function($query) {
                $query->select('code', 'name', 'product_line_code');
            }])->latest('id')
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

        $causeOfLoss = new CauseOfLoss();

        $this->assignValues($request, $causeOfLoss);

        if ($causeOfLoss->save()) {
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
                Rule::unique(CauseOfLoss::class, 'code')
                    ->ignore($model)
                    ->where('status', 'ACT'),
            ],
            'cause_name' => 'required',
        ]);
    }

    private function assignValues($request, $model) {
        $model->code = $request->code;
        $model->cause_name = $request->cause_name;
        $model->cause_name_kh = $request->cause_name_kh;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return CauseOfLoss::findOr($id, fn() => abort(404, 'Not found.'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $causeOfLoss = CauseOfLoss::findOr($id, fn() => abort(404, 'Not found.'));

        $this->validateRequest($request, $causeOfLoss);

        $this->assignValues($request, $causeOfLoss);

        if ($causeOfLoss->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $causeOfLoss = CauseOfLoss::findOr($id, fn() => abort(404, 'Not found.'));

        $causeOfLoss->status = "DEL";

        if ($causeOfLoss->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
