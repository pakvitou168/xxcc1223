<?php

namespace App\Http\Controllers\ReinsuranceConfig;

use Exception;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\ReinsuranceConfig\Reinsurance;
use Illuminate\Validation\ValidationException;

class ReinsuranceController extends Controller
{
  use DataTable;

  public function __construct()
  {
    $this->authorizeResource(Reinsurance::class, 'reinsurance');
  }


  public function index()
  {
    return $this->generateTableData(Reinsurance::where('status', 'ACT')->orderByDesc('id'));
  }

  public function create()
  {
    //
  }

  public function store(Request $request)
  {
    $validation = $this->validateRequest($request);
    if ($validation) {
      return $validation;
    }
    try {
      $reinsurance = new Reinsurance();
      $this->assignValues($request, $reinsurance);
      if ($reinsurance->save())
        return [
          'success' => true,
          'message' => "Reinsurance is successfully created!"
        ];
    } catch (\Throwable $th) {
      if($th instanceof ValidationException)
        return [
          'error' => $th->errors(),
          'message' => $th->getMessage()
        ];
      else
        return [
          'error' => true,
          'message' => $th->getMessage()
        ];
    }
  }

  public function show(Reinsurance $reinsurance)
  {
    try {
      if($reinsurance->status === 'DEL') throw new Exception('Record not found!');
      return $reinsurance;
    } catch (\Throwable $th) {
      Log::error('Reinsurance showing error : '.$th);
      return [
        'error' => true,
        'message' => $th->getMessage()
      ];
    }
  }

  public function edit(Reinsurance $reinsurance)
  {
    try {
      if($reinsurance->status === 'DEL') throw new Exception('Record not found!');
      return $reinsurance;
    } catch (\Throwable $th) {
      Log::error('Reinsurance edit error : '.$th);
      return [
        'error' => true,
        'message' => $th->getMessage()
      ];
    }
  }

  public function update(Request $request, Reinsurance $reinsurance)
  {
    $validation = $this->validateRequest($request);
    if ($validation) {
      return $validation;
    }
    try {
      if($reinsurance->status === 'DEL') throw new Exception('Record not found!');
      $this->assignValues($request,$reinsurance);
      if ($reinsurance->update()){
        return [
          'success' => true,
          'message' => 'Record is updated.'
        ];
      }
    } catch (\Throwable $th) {
      if($th instanceof ValidationException)
        return [
          'error' => $th->errors(),
          'message' => $th->getMessage()
        ];
      else
        return [
          'error' => true,
          'message' => $th->getMessage()
        ];
    }
  }

  public function destroy(Reinsurance $reinsurance)
  {
    try {
      if($reinsurance->status === 'DEL') throw new Exception('Record not found!');
      $reinsurance->status = 'DEL';
      if ($reinsurance->save()) {
        return [
          'success' => true,
          'message' => 'Record is deleted.'
        ];
      }
    } catch (\Throwable $th) {
      Log::error('Reinsurance delete error : '.$th);
      return [
        'error' => true,
        'message' => $th->getMessage()
      ];
    }

  }

  private function assignValues(Request $request, Reinsurance $reinsurance)
  {
    $reinsurance->code = $request->code;
    $reinsurance->name = $request->name;
    $reinsurance->description = $request->description;
  }

  private function validateRequest(Request $request)
  {
    $id = $request->id ?? '';
    $validator = Validator::make($request->all(), [
      'name' => ['required'],
      'code' => ['required',
                  Rule::unique(Reinsurance::class,'code')
                  ->ignore($id)
                  ->where(function ($query) {
                    return $query->where('status', 'ACT');
                  })
                ],
    ]);

    if ($validator->fails()) {
      Log::error('ReinsuranceController - Incorrect validate data input : ' . $validator->errors());
      return response()->json([
        'success' => false,
        'errors' => $validator->errors()
      ], 422);
    }
  }
}
