<?php

namespace App\Http\Controllers\PolicyWording;

use App\Http\Controllers\Controller;
use App\Models\ProductConfiguration\PolicyWordingVersion;
use App\Traits\DataTable;
use Illuminate\Http\Request;

class PolicyWordingVersionController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(PolicyWordingVersion::class, 'policy_wording_version');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            PolicyWordingVersion::with('product:code,name')
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
        $this->validateRequest($request);

        $policyWordingVersion = new PolicyWordingVersion();
        $this->assignValues($request, $policyWordingVersion);

        $this->updateIsDefault($request, $policyWordingVersion);

        if ($policyWordingVersion->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'product_line_code' => 'required',
            'product_code' => 'required',
            'policy_wording' => 'required',
            'year' => 'required|numeric'
        ]);
    }

    private function assignValues($request, $policyWordingVersion) {
        $policyWordingVersion->product_line_code = $request->product_line_code;
        $policyWordingVersion->product_code = $request->product_code;
        $policyWordingVersion->policy_wording = $request->policy_wording;
        $policyWordingVersion->year = $request->year;

        if ($request->is_default == 1) {
            $policyWordingVersion->is_default = "Y";
        } else {
            $policyWordingVersion->is_default = "N";
        }
    }

    /**
     * Set other record is_default to 'N' if current record is set to 'Y'
     *
     */
    private function updateIsDefault($request, $policyWordingVersion) {

        if ($policyWordingVersion->is_default === 'N') return;

        PolicyWordingVersion::where('product_code', $request->product_code)
            ->where('id', '<>', $policyWordingVersion->id)
            ->where('status', 'ACT')
            ->update(['is_default' => 'N']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductConfiguration\PolicyWordingVersion  $policyWordingVersion
     * @return \Illuminate\Http\Response
     */
    public function show(PolicyWordingVersion $policyWordingVersion)
    {
        $policyWordingVersion->load(['product' => function($query) {
            $query->select('code', 'name');
        }]);

        $policyWordingVersion->is_default = $policyWordingVersion->is_default === 'Y' ? true : false;

        return $policyWordingVersion;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductConfiguration\PolicyWordingVersion  $policyWordingVersion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PolicyWordingVersion $policyWordingVersion)
    {
        $this->validateRequest($request);

        $this->assignValues($request, $policyWordingVersion);

        $this->updateIsDefault($request, $policyWordingVersion);

        if ($policyWordingVersion->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductConfiguration\PolicyWordingVersion  $policyWordingVersion
     * @return \Illuminate\Http\Response
     */
    public function destroy(PolicyWordingVersion $policyWordingVersion)
    {
        $policyWordingVersion->status = "DEL";
        
        if ($policyWordingVersion->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
