<?php

namespace App\Http\Controllers\ProductConfiguration\ClauseMaintenance;

use App\Http\Controllers\Controller;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use App\Models\ClauseMaintenance\ClauseMaintenance;
use Illuminate\Support\Facades\DB;

class ClauseMaintenanceController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(ClauseMaintenance::class, 'clause_maintenance');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        return $this->generateTableData(
            ClauseMaintenance::where('status', 'ACT')
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
        $this->validateRequest($request);

        $clauseMaintenance = new ClauseMaintenance();
        $clauseMaintenance->code = $this->generateClauseCode();
        $this->assignValues($request, $clauseMaintenance);
        
        if ($clauseMaintenance->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'clause_type' => 'required',
            'clause' => 'required',
            'product_line_code' => 'required'
        ]);
    }

    private function assignValues($request, $clauseMaintenance) {
        $clauseMaintenance->product_line_code = $request->product_line_code;
        $clauseMaintenance->clause_type = $request->clause_type;
        $clauseMaintenance->clause = $request->clause;
        $clauseMaintenance->clause_detail = $request->clause_detail;
        $clauseMaintenance->clause_kh = $request->clause_kh;
        $clauseMaintenance->clause_detail_kh = $request->clause_detail_kh;
        $clauseMaintenance->clause_zh = $request->clause_zh;
        $clauseMaintenance->clause_detail_zh = $request->clause_detail_zh;
        $clauseMaintenance->sequence = $request->sequence;

        if($request->post('default_inclusion') == 1) {
            $clauseMaintenance->default_inclusion = "Y";
        } else {
            $clauseMaintenance->default_inclusion = "N";
        }
    }

    private function generateClauseCode() {
        return collect(DB::select('select * from ins_generate_clause_code()'))->first()->ins_generate_clause_code;        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClauseMaintenance\ClauseMaintenance  $clauseMaintenance
     * @return \Illuminate\Http\Response
     */
    public function show(ClauseMaintenance $clauseMaintenance)
    {
        $clauseMaintenance->default_inclusion = $clauseMaintenance->default_inclusion == 'Y' ? 1 : 0;
        return $clauseMaintenance;   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClauseMaintenance\ClauseMaintenance  $clauseMaintenance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClauseMaintenance $clauseMaintenance)
    {
        $this->validateRequest($request);

        $this->assignValues($request, $clauseMaintenance);

        if ($clauseMaintenance->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClauseMaintenance\ClauseMaintenance  $clauseMaintenance
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClauseMaintenance $clauseMaintenance)
    {   
        $clauseMaintenance->status="DEL";
        
        if ($clauseMaintenance->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
