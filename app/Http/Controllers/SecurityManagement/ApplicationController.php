<?php

namespace App\Http\Controllers\SecurityManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\SecurityManagement\ApplicationRequest;
use App\Repositories\SecurityManagement\ApplicationInterface;
use App\Traits\DataTable;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    use DataTable;
    public function __construct(private ApplicationInterface $applicationRepo) {
        // $this->middleware('has-permission:SM_APPLICATION|VIEW')->only('index', 'show');
        // $this->middleware('has-permission:SM_APPLICATION|NEW')->only('store');
        // $this->middleware('has-permission:SM_APPLICATION|UPD')->only('update');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $this->applicationRepo->query();
        return $this->generateTableData($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ApplicationRequest $request)
    {
        $data = $request->toArray();

        $org = $this->applicationRepo->create($data);

        if ($org) {
            return response()->json(['message' => 'App created successfully']);
        }
        return response()->json(['message' => 'Something went wrong...!'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $app = $this->applicationRepo->find($id);

        if ($app) {
            return response()->json(['application' => $app]);
        }
        return response()->json(['message' => 'Not found!'], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ApplicationRequest $request, $id)
    {
        $data = $request->toArray();

        $app = $this->applicationRepo->update($data, $id);

        if ($app) {
            return response()->json(['application' => $app,'message' => 'Record is updated successfully.']);
        }
        return response()->json(['message' => 'Something went wrong...!'], 500);
    }
}
