<?php

namespace App\Http\Controllers\ProductConfiguration\AccessRule;

use App\Http\Controllers\Controller;
use App\Models\Make\AccessRule;
use App\Traits\DataTable;
use Illuminate\Http\Request;

class AccessRuleController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(AccessRule::class, 'access_rule');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            AccessRule::with('user:id,username')
                ->with('make:id,make')
                ->with('model:id,model')
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

        $accessRule = new AccessRule();
        $this->assignValues($request, $accessRule);

        if ($accessRule->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'user_id' => 'required',
            'make_id' => 'required'
        ]);
    }

    private function assignValues($request, $accessRule) {
        $allowOffline = $request->allow_offline ?? false;
        $allowOnline = $request->allow_online ?? false;

        $accessRule->user_id = $request->user_id;
        $accessRule->make_id = $request->make_id;
        $accessRule->model_id = $request->model_id;

        $accessRule->allow_offline = $allowOffline ? 'Y' : 'N';
        $accessRule->allow_online = $allowOnline ? 'Y' : 'N';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Make\AccessRule  $accessRule
     * @return \Illuminate\Http\Response
     */
    public function show(AccessRule $accessRule)
    {
        $accessRule->load('user');
        $accessRule->load('make');
        $accessRule->load('model');

        $accessRule->allow_offline = $accessRule->allow_offline == 'Y' ? true : false;
        $accessRule->allow_online = $accessRule->allow_online == 'Y' ? true : false;

        return $accessRule;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Make\AccessRule  $accessRule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AccessRule $accessRule)
    {
        $this->validateRequest($request);

        $this->assignValues($request, $accessRule);

        if ($accessRule->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Make\AccessRule  $accessRule
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccessRule $accessRule)
    {
        $accessRule->status = "DEL";
        
        if ($accessRule->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
