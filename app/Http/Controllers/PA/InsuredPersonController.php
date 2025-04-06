<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PA\InsuredPersonRequest;
use App\Services\PA\InsuredPersonService;
use DB;
use Exception;
use Illuminate\Http\Request;

class InsuredPersonController extends Controller
{
    public function __construct(private InsuredPersonService $insuredPersonService)
    {

    }

    public function store(InsuredPersonRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->insuredPersonService->save($request->validated());
            DB::commit();
            return response()->json(['message' => 'Created successfully']);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['message' => 'Creating failed'],500);
        }
    }
    public function update(InsuredPersonRequest $request,$id)
    {
        try {
            DB::beginTransaction();
            $this->insuredPersonService->update($request->validated(),$id);
            DB::commit();
            return response()->json(['message' => 'Updated successfully']);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['message' => 'Updating failed'],500);
        }
    }
    public function deleteMulti(Request $request){
        try {
            DB::beginTransaction();
            $this->insuredPersonService->deleteMany($request->ids,$request->dataId);
            DB::commit();
            return response()->json(['message' => 'Deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'deleting failed'],500);
        }
    }
}
