<?php

use App\Http\Controllers\Claim\HS\HSPaymentController;
use App\Http\Controllers\Claim\HS\HSRegisterController;
use App\Http\Controllers\Clinic\ClinicController;
use App\Http\Controllers\Endorsement\HSEndorsementController;
use App\Http\Controllers\Endorsement\HSEndorsementServiceController;
use App\Http\Controllers\Policy\HSPolicyController;
use App\Http\Controllers\Policy\HSPolicyServiceController;
use App\Http\Controllers\Quotation\HSController;
use App\Http\Controllers\Reinsurance\HSReinsuranceDataController;
use App\Http\Controllers\ReinsuranceConfig\HSReinsuranceConfigController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'authenticated:security'], function () {
  Route::get('quotations/get-lovs', [HSController::class, 'getLovs']);
  Route::get('quotations/get-business-channels-lov/{saleChannelId}', [HSController::class, 'getBusinessChannelsLov']);
  Route::get('quotations/get-customers-lov/{customer_type}', [HSController::class, 'getCustomersLov']);
  Route::post('quotations/{id}/approve', [HSController::class, 'approve']);
  Route::post('quotations/{id}/accept', [HSController::class, 'accept']);
  Route::get('/show-detail/{id}', [HSController::class, 'showDetail']);
  Route::get('/{hs}/download-quotation/{lang}', [HSController::class, 'downloadQuotation']);
  Route::post('/proceed-to-policy/{quotation}', [HSController::class, 'proceedToPolicy']);
  Route::post('quotations/get-product-code-by-upload-excel', [HSController::class, 'getProductCodeByUploadExcel']);
  Route::post('quotations/get-product-code-from-upload-excel', [HSController::class, 'getProductCodeFromUploadExcel']);
  Route::get('quotations/find-business-channel/{businessCode}', [HSController::class, 'findBusinessChannel']);
  Route::put('/update-issue-date/{hs}', [HSController::class, 'updateIssuedDate']);
  Route::apiResource('quotations', HSController::class)->except(['update']);
  Route::get('/quotations/export-insured-persons/{dataId}/{quotationNo}', [HSController::class, 'exportInsuredPersons']);
  // Policies
  Route::apiResource('policies', HSPolicyController::class, ["as" => "hs"])->except([
    'store'
  ]);
  Route::prefix('policies')->group(function () {
    Route::post('/revise/{policy}', [HSPolicyController::class, 'revise']);
    Route::post('/approve/{policy}', [HSPolicyController::class, 'approve']);
    Route::get('/show-detail/{id}', [HSPolicyController::class, 'showDetail']);
    Route::post('/generate-hs-invoice', [HSPolicyController::class, 'generateHSInvoice']);
  });
  // Policy Services
  Route::prefix('/policy-services')->group(function () {
    Route::get('/list-business-types', [HSPolicyServiceController::class, 'listBusinessTypes']);
    Route::get('/list-policy-types', [HSPolicyServiceController::class, 'listPolicyTypes']);
    Route::get('{policy}/get-signature', [HSPolicyServiceController::class, 'getSignatureByPolicy']);
    Route::get('{id}/export-insured-persons/{document_no}', [HSPolicyServiceController::class, 'exportInsuredPersons']);
    Route::get('/{id}/download-invoice/{withSignature?}', [HSPolicyServiceController::class, 'downloadInvoice']);
    Route::get('/{id}/download-hs-certificate', [HSPolicyServiceController::class, 'downloadHSCertificate']);
    Route::get('/{policy}/download-policy-schedule/{lang}', [HSPolicyServiceController::class, 'downloadPolicySchedule']);
    Route::put('/update-submit-status/{policy}', [HSPolicyServiceController::class, 'updateSubmitStatus']);
    Route::get('export-reinsurance/{id}/{product_code}', [HSPolicyServiceController::class, 'exportReinsurance']);
    Route::get('/is-policy-configuration-completed/{policy}', [HSPolicyServiceController::class, 'isPolicyConfigurationCompleted']);

    // Policy Service Commission
    Route::get('/generate-commission-data/{policy_id}', [HSPolicyServiceController::class, 'generateCommissionData']);
    Route::get('/get-commission-data/{policy_id}', [HSPolicyServiceController::class, 'getCommissionData']);
    Route::get('/get-business-name-by-business-code/{business_code}', [HSPolicyServiceController::class, 'getBusinessNameByBusinessCode']);
    Route::get('/is-commission-data-available/{policy_id}', [HSPolicyServiceController::class, 'isCommissionDataAvailable']);
    Route::post('/update-commission-data/{policy_id}', [HSPolicyServiceController::class, 'updateCommissionData']);
    // Policy Service Export
    Route::prefix('/export-policy')->group(function () {
      Route::get('/from{issue_date_from?}/to{issue_date_to?}', [HSPolicyServiceController::class, 'exportPolicy']);
      Route::get('/from/to{issue_date_to?}', [HSPolicyServiceController::class, 'exportPolicy']);
    });
    // Policy Service Reinsurance
    Route::get('/generate-reinsurance-share/{policy_id}', [HSPolicyServiceController::class, 'generateReinsuranceShare']);
    Route::get('/generate-reinsurance-data/{policy_id}', [HSPolicyServiceController::class, 'generateReinsuranceData']);
    Route::get('/get-reinsurance-data/{policy_id}', [HSPolicyServiceController::class, 'getReinsuranceData']);
    Route::get('/check-if-share-under-limit/{policy_id}', [HSPolicyServiceController::class, 'checkIfShareUnderLimit']);
    Route::get('/is-policy-reinsurance-completed/{policy}', [HSPolicyServiceController::class, 'isPolicyReinsuranceCompleted']);
    Route::get('/list-treaty-codes', [HSPolicyServiceController::class, 'listTreatyCodes']);
    Route::get('/list-partner-groups', [HSPolicyServiceController::class, 'listPartnerGroupCodes']);
  });

  // Reinsurance Data
  Route::prefix('/reinsurance-data')->group(function () {
    Route::post('/', [HSReinsuranceDataController::class, 'store']);
    Route::get('/{policy_id}/get-sum', [HSReinsuranceDataController::class, 'getSum']);
  });

  // Reinsurance Config
  Route::prefix('/reinsurance-config')->group(function () {
    Route::get('/get-default-reinsurance-config/{code}', [HSReinsuranceConfigController::class, 'getDefaultReinsuranceConfig']);
  });

  //Endorsement
  Route::prefix('endorsements')->group(function () {
    Route::post('/{id}/generate', [HSEndorsementController::class, 'generate']);
    Route::get('/list-endorsement-types', [HSEndorsementController::class, 'listEndorsementTypes']);
    Route::get('/{id}/valid-period', [HSEndorsementController::class, 'getValidPeriod']);
    Route::get('/{id}/can-generate', [HSEndorsementController::class, 'canGenerate']);
    Route::post('/{endorsement}/approve', [HSEndorsementController::class, 'approve']);
    Route::get('/{endorsement}/show-detail', [HSEndorsementController::class, 'showDetail']);
    Route::get('/{endorsement}/get-premium/{endorsement_stage}/{raw_number?}', [HSEndorsementController::class, 'getPremium']);
    Route::put('/{endorsement}/save-cancel-policy-endorsement', [HSEndorsementController::class, 'saveCancelPolicyEndorsement']);
    Route::get('/get-detail/{id}', [HSEndorsementController::class, 'getDetail']);
    Route::patch('/{policy}/config', [HSEndorsementController::class, 'configure']);
    Route::get('/{policy}/export-template', [HSEndorsementController::class, 'exportTemplate']);
    Route::patch('/{policy}/import-endorsement', [HSEndorsementController::class, 'importEndorsement']);
    Route::post('/{endorsement}/update-description',[HSEndorsementController::class,'updateDesc']);
  });
  Route::apiResource('endorsements', HSEndorsementController::class);
  
  Route::prefix('/endorsement-services')->group(function () {
    Route::get('/{endorsement}/get-commission-data', [HSEndorsementServiceController::class, 'getCommissionData']);
    Route::get('/{endorsement}/get-reinsurance-data', [HSEndorsementServiceController::class, 'getReinsuranceData']);
    Route::get('/{endorsement}/is-commission-data-available', [HSEndorsementServiceController::class, 'isCommissionDataAvailable']);
    Route::get('{id}/export-insured-persons/{document_no}', [HSEndorsementServiceController::class, 'exportInsuredPersons']);
    Route::get('/{endorsement}/show-policy-cancellation-tab', [HSEndorsementServiceController::class, 'showPolicyCancellationTab']);
    Route::get('/list-refund-type-options/{policy_id}', [HSEndorsementServiceController::class, 'listRefundTypeOptions']);
    Route::get('/{endorsement}/download-invoice',[HSEndorsementServiceController::class,'printInvoice']);
    Route::get('/{endorsement}/download-endorsement',[HSEndorsementServiceController::class,'printEndorsement']);
    Route::get('/{endorsement}/download-credit-note',[HSEndorsementServiceController::class,'printCreditNote']);
    Route::post('/generate-invoice',[HSEndorsementServiceController::class,'generateInvoice']);
    Route::post('/generate-credit-note',[HSEndorsementServiceController::class,'generateCreditNote']);
    Route::get('/can-export-all-insured-person/{id}',[HSEndorsementServiceController::class,'canexportAll']);
    Route::get('/{id}/export-insured-person/{documentNo}',[HSEndorsementServiceController::class,'exportInsuredPersons']);
    Route::get('/{id}/export-all-insured-person/{documentNo}',[HSEndorsementServiceController::class,'exportAllInsuredPersons']);
  });
  // Route::prefix('/claim-service')->group(function () {
  //   Route::resource('/registers', HSRegisterController::class)->except('destroy');
  //   Route::post('/registers/{id}/approve',[HSRegisterController::class,'approve']);
  //   Route::post('/registers/{id}/revise',[HSRegisterController::class,'revise']);
  //   Route::get('/get-lovs', [HSRegisterController::class,'getLov']);
  //   Route::get('/get-insured-persons', [HSRegisterController::class,'getInsuredPerson']);
  // });
  Route::prefix('/claim-service')->group(function () {
    Route::resource('/registers', HSRegisterController::class);
    Route::post('/registers/{id}/approve',[HSRegisterController::class,'approve']);
    Route::get('/get-lovs', [HSRegisterController::class,'getLov']);
    Route::get('/filter-claimable-policies',[HSRegisterController::class,'filterPolicy']);
    Route::get('/get-insured-persons', [HSRegisterController::class,'getInsuredPerson']);
    Route::get('/get-cause-of-loss/{policyId}/{insuredId}',[HSRegisterController::class,'getCauseOfLoss']);
    Route::get('/register-pdf/{claim}/{lang}/{letterHead?}',[HSRegisterController::class,'pdfRegister']);
    Route::get('/register-schema-pdf/{claim}/{lang}/{letterHead?}',[HSRegisterController::class,'pdfSchema']);
    Route::get('/get-shema-data/{claim}',[HSRegisterController::class,'getSchema']);
    Route::post('/save-schema-data/{claim}',[HSRegisterController::class,'setSchema']);
    Route::post('/approve-schema-data/{claim}',[HSRegisterController::class,'approveSchema']);
    Route::post('/schema-revise/{claim}',[HSRegisterController::class,'reviseSchema']);
  });
  Route::apiResource('clinics',ClinicController::class);
  //Partial Payment
  Route::prefix('/claim-payment')->group(function () {
    Route::resource('/payments', HSPaymentController::class);
    Route::post('/payments/{id}/approve', [HSPaymentController::class,'approve']);
    Route::get('/payments/{id}/edit', [HSPaymentController::class,'edit']);
    Route::post('/payments/{id}/revise', [HSPaymentController::class,'revise']);
    Route::get('/claim-payments-list-cause-of-losses/{claim_no}', [HSPaymentController::class, 'listCauseOfLosses']);
    Route::get('/get-lovs', [HSPaymentController::class, 'getLov']);
    Route::get('/{id}/print/{lang}', [HSPaymentController::class, 'print']);
    Route::get('/{id}/print-cheque/{lang}', [HSPaymentController::class, 'print']);
    Route::get('/{id}/print-discharge/{lang}', [HSPaymentController::class, 'print']);
  });

  // Export Claim Reports
  Route::prefix('claim-report')->group(function () {
    Route::get('/type{report_type}/from{from_date?}/to{to_date?}', [HSRegisterController::class, 'exportClaim']);
    Route::get('/type{report_type}/from/to{to_date?}', [HSRegisterController::class, 'exportClaim']);
  });
});
