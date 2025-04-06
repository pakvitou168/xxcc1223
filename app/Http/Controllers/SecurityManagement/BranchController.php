<?php

namespace App\Http\Controllers\SecurityManagement;

use Throwable;
use Illuminate\Http\Request;
use App\Constants\ResponseCode;
use App\Constants\ResponseMessage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SecurityManagement\BranchRequest;
use App\Repositories\SecurityManagement\BranchInterface;
use App\Repositories\SecurityManagement\OrganizationInterface;

class BranchController extends Controller
{
    function __construct(
        private BranchInterface $branchRepository,
        private OrganizationInterface $orgRepository,
    ) {
        $this->middleware('has-permission:SM_BRANCH|VIEW')->only('index', 'show');
        $this->middleware('has-permission:SM_BRANCH|NEW')->only('store');
        $this->middleware('has-permission:SM_BRANCH|UPD')->only('update');
    }

    public function index(Request $request) {
        try {
            $result = $this->branchRepository->datatable(
                request: $request,
                query: [],
                searchable: ['code', 'name']
            );

            $result->getCollection()->transform(function ($res) {
                $res->org_id = $this->orgRepository->find($res->org_id)?->name;
                return $res;
            });

            return response_format(
                ['branches' => $result],
                ResponseMessage::QUERY_SUCCESS
            );
        } catch(Throwable $e) {
            Log::error('BRANCH INDEX: '.$e->getMessage());
            return response_format(
                [],
                ResponseMessage::SOMETHING_WRONG,
                false,
                ResponseCode::INTERNAL_ERROR
            );
        }
    }

    public function store(BranchRequest $request) {
        try {
            $result = $this->branchRepository->create($request->validated());

            return response_format(
                ['id' => $result->id],
                ResponseMessage::CREATE_SUCCESS
            );
        } catch(Throwable $e){
            Log::error('BRANCH STORE: '.$e->getMessage());
            return response_format(
                [],
                ResponseMessage::SOMETHING_WRONG,
                false,
                ResponseCode::INTERNAL_ERROR
            );
        }
    }

    public function show($id) {
        try {
            $result = $this->branchRepository->find($id);
            return response_format(
                ['branches' => $result],
                ResponseMessage::QUERY_SUCCESS
            );
        } catch(Throwable $e){
            Log::error('BRANCH SHOW: '.$e->getMessage());
            return response_format(
                [],
                ResponseMessage::SOMETHING_WRONG,
                false,
                ResponseCode::INTERNAL_ERROR
            );
        }
    }

    public function destroy(Request $request) {}

    public function update(BranchRequest $request) {
        try {
            $result =  $this->branchRepository->update($request->validated(), $request->id);
            return response_format(
                ['branch' => $result],
                ResponseMessage::UPDATE_SUCCESS
            );
        } catch(Throwable $e){
            Log::error('BRANCH UPDATE: '.$e->getMessage());
            return response_format(
                [],
                ResponseMessage::SOMETHING_WRONG,
                false,
                ResponseCode::INTERNAL_ERROR
            );
        }
    }

    public function listOrganization(Request $request) {
        try {
            $result = $this->orgRepository->allByStatusActive();
            return response_format(
                $result,
                ResponseMessage::QUERY_SUCCESS
            );
        } catch(Throwable $e) {
            Log::error('BRANCH LIST ORGANIZATION: '.$e->getMessage());
            return response_format(
                [],
                ResponseMessage::SOMETHING_WRONG,
                false,
                ResponseCode::INTERNAL_ERROR
            );
        }
    }
}
