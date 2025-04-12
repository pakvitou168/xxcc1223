<?php

use App\Http\Controllers\Travel\Claim\RegisterController;
use App\Http\Controllers\Travel\QuotationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Travel\Policy\PolicyController;
use App\Http\Controllers\Travel\Policy\PolicyApprovalController;
use App\Http\Controllers\Travel\Policy\PolicyExportController;
use App\Http\Controllers\Travel\Policy\PolicyVerificationController;
use App\Http\Controllers\Travel\Policy\PolicyLovController;
use App\Http\Controllers\Travel\Policy\PolicyDeductibleDataController;
use App\Http\Controllers\Travel\Policy\EndorsementController;
use App\Http\Controllers\Travel\Policy\ReinsuranceConfigController;
use App\Http\Controllers\Travel\Policy\PolicyServiceController;
use App\Http\Controllers\Reinsurance\TravelReinsuranceDataController;
use App\Http\Controllers\Endorsement\TravelEndorsementController;
use App\Http\Controllers\Endorsement\TravelEndorsementServiceController;
use \App\Http\Controllers\ProductConfiguration\ClauseMaintenance\Travel\ClauseMaintenanceServiceController;


Route::get('test', function () {
    $mock_policy = \App\Models\Travel\Policy\Policy::whereHas('dataMaster', function($q) {
        $q->whereHas('customer');
    })
        ->whereHas('invoiceNote')
        ->with([
            'dataMaster.customer',
            'invoiceNote',
        ])
        ->whereNotNull("approved_by")
        ->first();
    $id = $mock_policy->id;
    $withSignature="withSignature";
     dd($mock_policy);
    dd(213);

});
Route::get('invoice/{id}/{withSignature?}', [PolicyServiceController::class, 'downloadInvoice']);

Route::get('/{policy}/download-policy-schedule/{lang}', [PolicyServiceController::class, 'downloadPolicySchedule']);

Route::group(['middleware' => 'authenticated:security'], function () {
    Route::prefix('/quotation-service')->group(function () {
        Route::get('/selections', [QuotationController::class, 'selection']);
        Route::get('/business-options/{businessChannel}', [QuotationController::class, 'businessOption']);
        Route::post('/search-insured-persons', [QuotationController::class, 'searchInsuredPerson']);
        Route::post('/validate-file', [QuotationController::class, 'validateFileUpload']);
    });
    Route::prefix('/quotations')->group(function () {
        Route::patch('/{id}/approve', [QuotationController::class, 'approve']);
        Route::patch('/{id}/accept', [QuotationController::class, 'accept']);
        Route::get('/{id}/proceed', [QuotationController::class, 'proceed']);
        Route::get('/{id}/export/insured-persons', [QuotationController::class, 'exportInsuredPerson']);
        Route::get('/{id}/download/{lang}', [QuotationController::class, 'print']);
    });
    Route::resource('/quotations', QuotationController::class);

    Route::prefix('/claim-service')->group(function () {
        Route::resource('/registers', RegisterController::class);
        Route::post('/registers/{id}/approve', [RegisterController::class, 'approve']);
        Route::get('/get-lovs', [RegisterController::class, 'getLov']);
        Route::get('/filter-claimable-policies', [RegisterController::class, 'filterPolicy']);
        Route::get('/get-insured-persons', [RegisterController::class, 'getInsuredPerson']);
        Route::get('/get-cause-of-loss/{policyId}/{insuredId}', [RegisterController::class, 'getCauseOfLoss']);
        Route::get('/register-pdf/{claim}/{lang}/{letterHead?}', [RegisterController::class, 'pdfRegister']);
        Route::get('/register-schema-pdf/{claim}/{lang}/{letterHead?}', [RegisterController::class, 'pdfSchema']);
        Route::get('/get-shema-data/{claim}', [RegisterController::class, 'getSchema']);
        Route::post('/save-schema-data/{claim}', [RegisterController::class, 'setSchema']);
        Route::post('/approve-schema-data/{claim}', [RegisterController::class, 'approveSchema']);
        Route::post('/schema-revise/{claim}', [RegisterController::class, 'reviseSchema']);
    });

    //Endorsement
    Route::prefix('endorsements')->group(function () {
        Route::post('/{id}/generate', [TravelEndorsementController::class, 'generate']);
        Route::get('/list-endorsement-types', [TravelEndorsementController::class, 'listEndorsementTypes']);
        Route::get('/{id}/valid-period', [TravelEndorsementController::class, 'getValidPeriod']);
        Route::get('/{id}/can-generate', [TravelEndorsementController::class, 'canGenerate']);
        Route::post('/{endorsement}/approve', [TravelEndorsementController::class, 'approve']);
        Route::get('/{endorsement}/show-detail', [TravelEndorsementController::class, 'showDetail']);
        Route::get('/{endorsement}/get-premium/{endorsement_stage}/{raw_number?}', [TravelEndorsementController::class, 'getPremium']);
        Route::put('/{endorsement}/save-cancel-policy-endorsement', [TravelEndorsementController::class, 'saveCancelPolicyEndorsement']);
        Route::get('/get-detail/{id}', [TravelEndorsementController::class, 'getDetail']);
        Route::patch('/{policy}/config', [TravelEndorsementController::class, 'configure']);
        Route::get('/{policy}/export-template', [TravelEndorsementController::class, 'exportTemplate']);
        Route::patch('/{policy}/import-endorsement', [TravelEndorsementController::class, 'importEndorsement']);
        Route::post('/{endorsement}/update-description', [TravelEndorsementController::class, 'updateDesc']);
    });
    Route::resource('endorsements', TravelEndorsementController::class);

    Route::prefix('/endorsement-services')->group(function () {
        Route::get('/{endorsement}/get-commission-data', [TravelEndorsementServiceController::class, 'getCommissionData']);
        Route::get('/{endorsement}/get-reinsurance-data', [TravelEndorsementServiceController::class, 'getReinsuranceData']);
        Route::get('/{endorsement}/is-commission-data-available', [TravelEndorsementServiceController::class, 'isCommissionDataAvailable']);
        Route::get('{id}/export-insured-persons/{document_no}', [TravelEndorsementServiceController::class, 'exportInsuredPersons']);
        Route::get('/{endorsement}/show-policy-cancellation-tab', [TravelEndorsementServiceController::class, 'showPolicyCancellationTab']);
        Route::get('/list-refund-type-options/{policy_id}', [TravelEndorsementServiceController::class, 'listRefundTypeOptions']);
        Route::get('/{endorsement}/download-invoice',[TravelEndorsementServiceController::class,'printInvoice']);
        Route::get('/{endorsement}/download-endorsement',[TravelEndorsementServiceController::class,'printEndorsement']);
        Route::get('/{endorsement}/download-credit-note',[TravelEndorsementServiceController::class,'printCreditNote']);
        Route::post('/generate-invoice',[TravelEndorsementServiceController::class,'generateInvoice']);
        Route::post('/generate-credit-note',[TravelEndorsementServiceController::class,'generateCreditNote']);
        Route::get('/can-export-all-insured-person/{id}',[TravelEndorsementServiceController::class,'canexportAll']);
        Route::get('/{id}/export-insured-person/{documentNo}',[TravelEndorsementServiceController::class,'exportInsuredPersons']);
        Route::get('/{id}/export-all-insured-person/{documentNo}',[TravelEndorsementServiceController::class,'exportAllInsuredPersons']);
    });

    Route::prefix('policies')->name('policies.')->group(function () {

         Route::get('/{id}/export/insured-persons', [PolicyServiceController::class, 'exportInsuredPerson']);
        // Policy Services
        Route::prefix('/policy-services')->group(function () {
            Route::get('/list-business-types', [PolicyServiceController::class, 'listBusinessTypes']);
            Route::get('/list-policy-types', [PolicyServiceController::class, 'listPolicyTypes']);
            Route::get('{policy}/get-signature', [PolicyServiceController::class, 'getSignatureByPolicy']);
            Route::get('{id}/export-insured-persons/{document_no}', [PolicyServiceController::class, 'exportInsuredPersons']);
            Route::get('/{id}/download-invoice/{withSignature?}', [PolicyServiceController::class, 'downloadInvoice']);
            Route::get('/{id}/download-travel-certificate', [PolicyServiceController::class, 'downloadCertificate']);
            Route::get('/{policy}/download-policy-schedule/{lang}', [PolicyServiceController::class, 'downloadPolicySchedule']);
            //Route::put('/update-submit-status/{policy}', [PolicyServiceController::class, 'updateSubmitStatus']);
            Route::get('export-reinsurance/{id}/{product_code}', [PolicyServiceController::class, 'exportReinsurance']);
            Route::get('/is-policy-configuration-completed/{policy}', [PolicyServiceController::class, 'isPolicyConfigurationCompleted']);

            // Policy Service Commission
            Route::get('/generate-commission-data/{policy_id}', [PolicyServiceController::class, 'generateCommissionData']);
            Route::get('/get-commission-data/{policy_id}', [PolicyServiceController::class, 'getCommissionData']);
            Route::get('/get-business-name-by-business-code/{business_code}', [PolicyServiceController::class, 'getBusinessNameByBusinessCode']);
            Route::get('/is-commission-data-available/{policy_id}', [PolicyServiceController::class, 'isCommissionDataAvailable']);
            Route::post('/update-commission-data/{policy_id}', [PolicyServiceController::class, 'updateCommissionData']);
            // Policy Service Export
            Route::prefix('/export-policy')->group(function () {
                Route::get('/from{issue_date_from?}/to{issue_date_to?}', [PolicyServiceController::class, 'exportPolicy']);
                Route::get('/from/to{issue_date_to?}', [PolicyServiceController::class, 'exportPolicy']);
            });
            // Policy Service Reinsurance
            Route::get('/generate-reinsurance-share/{policy_id}', [PolicyServiceController::class, 'generateReinsuranceShare']);
            Route::get('/generate-reinsurance-data/{policy_id}', [PolicyServiceController::class, 'generateReinsuranceData']);
            Route::get('/get-reinsurance-data/{policy_id}', [PolicyServiceController::class, 'getReinsuranceData']);
            Route::get('/check-if-share-under-limit/{policy_id}', [PolicyServiceController::class, 'checkIfShareUnderLimit']);
            Route::get('/is-policy-reinsurance-completed/{policy}', [PolicyServiceController::class, 'isPolicyReinsuranceCompleted']);
            Route::get('/list-treaty-codes', [PolicyServiceController::class, 'listTreatyCodes']);
            Route::get('/list-partner-groups', [PolicyServiceController::class, 'listPartnerGroupCodes']);
        });

        // Reinsurance Data
        Route::prefix('/reinsurance-data')->group(function () {
            Route::post('/', [TravelReinsuranceDataController::class, 'store']);
            Route::get('/{policy_id}/get-sum', [TravelReinsuranceDataController::class, 'getSum']);
        });

//        Route::put('/{policy}', [PolicyController::class, 'update'])->name('update');
//        Route::put('/update-issue-date/{id}', [PolicyController::class, 'updateIssuedDate']);

        Route::prefix('/reinsurance-config')->group(function () {
            Route::get('/get-default-reinsurance-config/{code}', [ReinsuranceConfigController::class, 'getDefaultReinsuranceConfig']);
        });

        Route::get('/endorsements/{policy}', [EndorsementController::class, 'show'])->name('endorsements.show');
        Route::put('/endorsements/{policy}', [EndorsementController::class, 'update'])->name('endorsements.update');
        Route::put('/update-submit-status/{policy}', [PolicyVerificationController::class, 'updateSubmitStatus']);

        Route::get('/get-deductible-data/{data_id}', [PolicyDeductibleDataController::class, 'getDeductibleData'])->name('deductible.data');

        Route::get('/get-lovs', [PolicyLovController::class, 'getLov'])->name('lov');

        Route::post('/approve/{policy}', [PolicyApprovalController::class, 'approve'])->name('approve');

        Route::get('/is-reinsurance-completed/{policy}', [PolicyVerificationController::class, 'isPolicyReinsuranceCompleted'])->name('reinsurance.completed');
        Route::get('/is-configuration-completed/{policy}', [PolicyVerificationController::class, 'isPolicyConfigurationCompleted'])->name('configuration.completed');

        Route::delete('/export/from{issue_date_from?}/to{issue_date_to?}', [PolicyExportController::class, 'exportPolicy'])->name('export');

        Route::get('/', [PolicyController::class, 'index'])->name('index');
        Route::get('/{policy}', [PolicyController::class, 'show'])->name('show');
        Route::delete('/{policy}', [PolicyController::class, 'destroy'])->name('destroy');
        Route::put('/{policy}', [PolicyController::class, 'update'])->name('update');
        Route::put('/update-issue-date/{policy}', [PolicyController::class, 'updateIssuedDate']);
    });
    Route::get('/clause-maintenance-service/clause-type/{productLine}', [ClauseMaintenanceServiceController::class, 'clauseType']);
});
