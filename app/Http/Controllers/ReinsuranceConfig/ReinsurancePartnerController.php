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
use App\Models\ReinsuranceConfig\ReinsurancePartner;

class ReinsurancePartnerController extends Controller
{
  use DataTable;

  public function __construct()
  {
    $this->authorizeResource(ReinsurancePartner::class, 'reinsurance_partner');
  }

  public function index()
  {
    return $this->generateTableData(ReinsurancePartner::where('status', 'ACT')->orderByDesc('id'));
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
      $reinsurance = new ReinsurancePartner();
      $this->assignValues($request, $reinsurance);
      if ($reinsurance->save())
        return [
          'success' => true,
          'message' => "Reinsurance partner is successfully created!"
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


  public function show(ReinsurancePartner $reinsurancePartner)
  {
    try {
      if($reinsurancePartner->status === 'DEL') throw new Exception('Record not found!');
      return $reinsurancePartner;
    } catch (\Throwable $th) {
      Log::error('Reinsurance partner showing error : '.$th);
      return [
        'error' => true,
        'message' => $th->getMessage()
      ];
    }
  }


  public function edit(ReinsurancePartner $reinsurancePartner)
  {
    try {
      if($reinsurancePartner->status === 'DEL') throw new Exception('Record not found!');
      return $reinsurancePartner;
    } catch (\Throwable $th) {
      Log::error('Reinsurance partner edit error : '.$th);
      return [
        'error' => true,
        'message' => $th->getMessage()
      ];
    }
  }

  public function update(Request $request, ReinsurancePartner $reinsurancePartner)
  {
    $validation = $this->validateRequest($request);
    if ($validation) {
        return $validation;
    }
    try {
      if($reinsurancePartner->status === 'DEL') throw new Exception('Record not found!');
      $this->assignValues($request,$reinsurancePartner);
      if ($reinsurancePartner->update()){
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


  public function destroy(ReinsurancePartner $reinsurancePartner)
  {
    try {
      if($reinsurancePartner->status === 'DEL') throw new Exception('Record not found!');
      $reinsurancePartner->status = 'DEL';
      if ($reinsurancePartner->save()) {
        return [
          'success' => true,
          'message' => 'Record is deleted.'
        ];
      }
    } catch (\Throwable $th) {
      Log::error('Reinsurance partner delete error : '.$th);
      return [
        'error' => true,
        'message' => $th->getMessage()
      ];
    }
  }

  private function assignValues(Request $request, ReinsurancePartner $reinsurancePartner)
  {
    $reinsurancePartner->code = $request->code;
    $reinsurancePartner->name = $request->name;
    $reinsurancePartner->description = $request->description;
    $reinsurancePartner->group_code = $request->group_code;
  }

  private function validateRequest(Request $request)
  {
    $id = $request->id ?? '';
    
    $validator = Validator::make($request->all(), [
        'name' => ['required'],
        'code' => [
            'required',
            Rule::unique(ReinsurancePartner::class, 'code')
                ->ignore($id)
                ->where(function ($query) {
                    return $query->where('status', 'ACT');
                })
        ],
        'group_code' => ['required'],
    ]);

    if ($validator->fails()) {
        Log::error('ReinsurancePartnerController - Incorrect validate data input : ' . $validator->errors());
        abort(response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422));
    }
  }
}
