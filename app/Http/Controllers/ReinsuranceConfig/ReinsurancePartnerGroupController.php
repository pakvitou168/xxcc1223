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
use App\Models\ReinsuranceConfig\ReinsurancePartnerGroup;

class ReinsurancePartnerGroupController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(ReinsurancePartnerGroup::class, 'reinsurance_partner_group');
    }

    public function index()
    {
        return $this->generateTableData(ReinsurancePartnerGroup::where('status', 'ACT')->orderByDesc('id'));
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
            $reinsurance = new ReinsurancePartnerGroup();
            $this->assignValues($request, $reinsurance);
            if ($reinsurance->save())
                return [
                    'success' => true,
                    'message' => "Reinsurance partner is successfully created!"
                ];
        } catch (\Throwable $th) {
            if ($th instanceof ValidationException)
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


    public function show(ReinsurancePartnerGroup $reinsurancePartnerGroup)
    {
        try {
            if ($reinsurancePartnerGroup->status === 'DEL') throw new Exception('Record not found!');
            return $reinsurancePartnerGroup;
        } catch (\Throwable $th) {
            Log::error('Reinsurance partner showing error : ' . $th);
            return [
                'error' => true,
                'message' => $th->getMessage()
            ];
        }
    }


    public function edit(ReinsurancePartnerGroup $reinsurancePartnerGroup)
    {
        try {
            if ($reinsurancePartnerGroup->status === 'DEL') throw new Exception('Record not found!');
            return $reinsurancePartnerGroup;
        } catch (\Throwable $th) {
            Log::error('Reinsurance partner edit error : ' . $th);
            return [
                'error' => true,
                'message' => $th->getMessage()
            ];
        }
    }

    public function update(Request $request, ReinsurancePartnerGroup $reinsurancePartnerGroup)
    {
        $validation = $this->validateRequest($request);
        if ($validation) {
            return $validation;
        }
        try {
            if ($reinsurancePartnerGroup->status === 'DEL') throw new Exception('Record not found!');
            $this->assignValues($request, $reinsurancePartnerGroup);
            if ($reinsurancePartnerGroup->update()) {
                return [
                    'success' => true,
                    'message' => 'Record is updated.'
                ];
            }
        } catch (\Throwable $th) {
            if ($th instanceof ValidationException)
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


    public function destroy(ReinsurancePartnerGroup $reinsurancePartnerGroup)
    {
        try {
            if ($reinsurancePartnerGroup->status === 'DEL') throw new Exception('Record not found!');
            $reinsurancePartnerGroup->status = 'DEL';
            if ($reinsurancePartnerGroup->save()) {
                return [
                    'success' => true,
                    'message' => 'Record is deleted.'
                ];
            }
        } catch (\Throwable $th) {
            Log::error('Reinsurance partner delete error : ' . $th);
            return [
                'error' => true,
                'message' => $th->getMessage()
            ];
        }
    }

    private function assignValues(Request $request, ReinsurancePartnerGroup $reinsurancePartnerGroup)
    {
        $reinsurancePartnerGroup->code = $request->code;
        $reinsurancePartnerGroup->name = $request->name;
        $reinsurancePartnerGroup->description = $request->description;
        $reinsurancePartnerGroup->group_code = $request->group_code;
    }

    private function validateRequest(Request $request)
    {
        $id = $request->id ?? '';
        
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'code' => [
                'required',
                Rule::unique(ReinsurancePartnerGroup::class, 'code')
                    ->ignore($id)
                    ->where(function ($query) {
                        return $query->where('status', 'ACT');
                    })
            ],
            'group_code' => ['required'],
        ]);

        if ($validator->fails()) {
            Log::error('ReinsurancePartnerGroupController - Incorrect validate data input : ' . $validator->errors());
            abort(response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422));
        }
    }
}
