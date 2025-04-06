<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PA\EndorsementUpdateInfoRequest;
use App\Services\PA\EndorsementService;
use App\Traits\SMDataTable;
use DB;
use Exception;
use Illuminate\Http\Request;

class EndorsementController extends Controller
{
    use SMDataTable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(private EndorsementService $endorsementService)
    {

    }
    public function index()
    {
        $data = $this->generateTableData($this->endorsementService->findAll());
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->json($this->endorsementService->edit($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EndorsementUpdateInfoRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->endorsementService->update($request->validated(),$id);
            DB::commit();
            return response()->json(['message' => 'Update success']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Something went wrong'], 500);
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
        //
    }
    public function info($id)
    {
        return response()->json($this->endorsementService->info($id));
    }
}
