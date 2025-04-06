<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Http\Requests\PA\OptionalExtRequest;
use App\Imports\PA\QuotationImport;
use App\Services\PA\BaseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ServiceController extends Controller
{
    public function __construct(private BaseService $baseService)
    {

    }
    public function selection()
    {
        return response()->json([
            'productTypeOpts' => $this->baseService->productTypeOptions(),
            'jointStatusOpts' => $this->baseService->jointOptions(),
            'customerTypeOpts' => $this->baseService->customerTypeOptions(),
            'jointLevelOpts' => $this->baseService->jointLevelOptions(),
            'permissionOpts' => $this->baseService->permissionOptions(),
            'endorsementClauseOpts' => $this->baseService->endorsementClauseOptions(),
            'generalExclusionOpts' => $this->baseService->generalExclusionOptions(),
            'businessChannelOpts' => $this->baseService->businessChannelOptions(),
            'businessHandlerOpts' => $this->baseService->businessHandlerOptions(),
            'geoGraphicalLimitOpts' => $this->baseService->geographicalLimitOptions(),
            'policyWordingOpts' => $this->baseService->policyWordingOptions(),
            'defaultEndorsementClause' => $this->baseService->defaultEndorsementClause(),
            'defaultGeneralExclusion' => $this->baseService->defaultGeneralExclusion(),
            'defaultAutoExtension' => $this->baseService->defaultAutoExtension(),
            'calcOpts' => $this->baseService->calcOptions(),
            'periodOpts' => $this->baseService->periodOptions(),
            'optionalBnfOpts' => $this->baseService->optionalBnfOptions(),
            'ofOpts' => $this->baseService->optionalBnfBaseOptions(),
            'quotationTmp' => $this->baseService->quotationTmp(),
            'autoExtClauseOpts' => $this->baseService->autoExtClauseOptions()
        ]);
    }
    public function searchInsuredPerson(Request $request)
    {
        return response()->json($this->baseService->searchInsuredPerson($request));
    }
    public function validateFileUpload(Request $request)
    {
        $this->validate($request, [
            'file' => ['required', 'mimes:xlsx']
        ]);
        try {
            $data = Excel::toArray(new QuotationImport(), $request->file('file'));
            $insuredPersons = isset($data['Name List']) ? $data['Name List'] : throw new Exception("Name List sheet not found");

            $this->baseService->validateInsuredData($insuredPersons);
            return response()->json(['success' => true, 'message' => 'Validation success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'errors' => [$e->getMessage()]], 500);
        }
    }
    public function businessOption(Request $request, $businessChannel)
    {
        return response()->json($this->baseService->businessOptions($businessChannel));
    }
    public function classOption()
    {
        return response()->json(
            $this->baseService->classOptions()
        );
    }
    public function optionalBnfBase()
    {
        return response()->json($this->baseService->optionalBnfBaseOptions());
    }
    public function optionalBnfData($id)
    {
        return response()->json($this->baseService->optionExtensionData($id));
    }
    public function updateOptBnf(OptionalExtRequest $request, $id)
    {
        try {
            $this->baseService->updateOtpExt($request->validated()['optional_benefits'], $id);
            return response()->json(['message' => 'Updated successfully']);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
    public function businessInfo($code)
    {
        return response()->json($this->baseService->businessInfo($code));
    }
    public function policyConfig()
    {
        return response()->json([
            'businessTypes' => $this->baseService->businessTypeOptions(),
            'policyTypes' => $this->baseService->policyTypeOptions()
        ]);
    }
    public function reinsuranceSelection(Request $request)
    {
        return response()->json([
            'groups' => $this->baseService->reinsuranceGroupOptions(),
            'defaultGroups' => $this->baseService->reinsurancedefaultGroups($request->productCode),
            'participants' => $this->baseService->participantOptions()
        ]);
    }
    public function endorsementType()
    {
        return response()->json($this->baseService->endorsementOptions());
    }
}
