<?php

namespace App\Http\Controllers\SecurityManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\SecurityManagement\OrganizationRequest;
use App\Repositories\SecurityManagement\OrganizationInterface;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function __construct(private OrganizationInterface $organizationRepo) {
        $this->middleware('has-permission:SM_ORGANIZATION|VIEW')->only('index', 'show');
        $this->middleware('has-permission:SM_ORGANIZATION|NEW')->only('store');
        $this->middleware('has-permission:SM_ORGANIZATION|UPD')->only('update');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $this->organizationRepo->query();

        $searchableCols = ['code', 'name'];

        $data = $this->organizationRepo->datatable(
            request: $request,
            query: $query,
            searchable: $searchableCols,
        );

        return response([
            'msg' => 'success',
            'data' => [
                'organizations' => $data,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrganizationRequest $request)
    {
        $data = $request->toArray();

        $org = $this->organizationRepo->create($data);

        if ($org) {
            return response_format(['organization' => $org], 'Record is created successfully.');
        }
        return response_format([], 'Something went wrong...!', false, 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $org = $this->organizationRepo->find($id);

        if ($org) {
            return response_format(['organization' => $org]);
        }
        return response_format([], 'Not found!', false, 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrganizationRequest $request, $id)
    {
        $data = $request->toArray();

        $org = $this->organizationRepo->update($data, $id);

        if ($org) {
            return response_format(['organization' => $org], 'Record is updated successfully.');
        }
        return response_format([], 'Something went wrong...!', false, 500);
    }
}
