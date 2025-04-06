<?php

namespace App\Http\Controllers\ReinsuranceConfig;

use Exception;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\ReinsuranceConfig\ReinsuranceType;

class ReinsuranceTypeController extends Controller
{
  use DataTable;

  public function __construct()
  {
    $this->authorizeResource(ReinsuranceType::class, 'reinsurance_type');
  }


  public function index()
  {
    return $this->generateTableData(ReinsuranceType::where('status', 'ACT')->orderByDesc('id'));
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
      $reinsurance = new ReinsuranceType();
      $this->assignValues($request, $reinsurance);
      if ($reinsurance->save())
        return [
          'success' => true,
          'message' => "Reinsurance type is successfully created!"
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

  public function show(ReinsuranceType $reinsuranceType)
  {
    try {
      if($reinsuranceType->status === 'DEL') throw new Exception('Record not found!');
      return $reinsuranceType;
    } catch (\Throwable $th) {
      Log::error('Reinsurance type showing error : '.$th);
      return [
        'error' => true,
        'message' => $th->getMessage()
      ];
    }
  }


  public function edit(ReinsuranceType $reinsuranceType)
  {
    try {
      if($reinsuranceType->status === 'DEL') throw new Exception('Record not found!');
      return $reinsuranceType;
    } catch (\Throwable $th) {
      Log::error('Reinsurance type edit error : '.$th);
      return [
        'error' => true,
        'message' => $th->getMessage()
      ];
    }
  }


  public function update(Request $request, ReinsuranceType $reinsuranceType)
  {
    $validation = $this->validateRequest($request);
    if ($validation) {
        return $validation;
    }
    try {
      if($reinsuranceType->status === 'DEL') throw new Exception('Record not found!');
      $this->assignValues($request,$reinsuranceType);
      if ($reinsuranceType->update()){
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


  public function destroy(ReinsuranceType $reinsuranceType)
  {
    try {
      if($reinsuranceType->status === 'DEL') throw new Exception('Record not found!');
      $reinsuranceType->status = 'DEL';
      if ($reinsuranceType->save()) {
        return [
          'success' => true,
          'message' => 'Record is deleted.'
        ];
      }
    } catch (\Throwable $th) {
      Log::error('Reinsurance type delete error : '.$th);
      return [
        'error' => true,
        'message' => $th->getMessage()
      ];
    }
  }

  private function assignValues(Request $request, ReinsuranceType $reinsurance)
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
        'code' => [
            'required',
            Rule::unique(ReinsuranceType::class, 'code')
                ->ignore($id)
                ->where(function ($query) {
                    return $query->where('status', 'ACT');
                })
        ],
    ]);

    if ($validator->fails()) {
        Log::error('ReinsuranceTypeController - Incorrect validate data input : ' . $validator->errors());
        abort(response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422));
    }
  }
}
