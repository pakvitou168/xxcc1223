<?php

use App\Http\Controllers\PA\EndorsementController;
use App\Http\Controllers\PA\InsuredPersonController;
use App\Http\Controllers\PA\PolicyController;
use App\Http\Controllers\PA\QuotationController;
use App\Http\Controllers\PA\ServiceController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['authenticated:security']], function () {
    
    Route::prefix('/services')->group(function () {
        Route::get('/selections', [ServiceController::class, 'selection']);
        Route::get('/business-options/{businessChannel}', [ServiceController::class, 'businessOption']);
        Route::post('/search-insured-persons', [ServiceController::class, 'searchInsuredPerson']);
        Route::post('/validate-file', [ServiceController::class, 'validateFileUpload']);
        Route::get('/classes',[ServiceController::class,'classOption']);
        Route::get('/option-extensions-base',[ServiceController::class,'optionalBnfBase']);
        Route::get('/{dataID}/optional-extensions',[ServiceController::class,'optionalBnfData']);
        Route::patch('/{dataID}/update-optional-extensions',[ServiceController::class,'updateOptBnf']);
        Route::get('/{bCode}/business-info',[ServiceController::class,'businessInfo']);
        Route::get('/policy-config',[ServiceController::class,'policyConfig']);
        Route::get('/reinsurance-selection',[ServiceController::class,'reinsuranceSelection']);
        Route::get('/endorsement-types',[ServiceController::class,'endorsementType']);
    });
    Route::resource('/quotations', QuotationController::class);
    Route::prefix('/quotation')->group(function () {
        Route::patch('/{id}/approve', [QuotationController::class, 'approve']);
        Route::patch('/{id}/accept', [QuotationController::class, 'accept']);
        Route::get('/{id}/proceed', [QuotationController::class, 'proceed']);
        Route::get('/{id}/insured-persons', [QuotationController::class, 'insuredPerson']);
        Route::get('/{id}/export/insured-persons', [QuotationController::class, 'exportInsuredPerson']);
        Route::get('/{id}/download/{lang}', [QuotationController::class, 'print']);
    });
    
    Route::prefix('policies')->group(function() {
        Route::get('/{dataId}/commission',[PolicyController::class,'commission']);
        Route::patch('/{dataId}/update-commission',[PolicyController::class,'updateCommission']);
        Route::get('/{dataId}/config',[PolicyController::class,'configuration']);
        Route::patch('/{dataId}/update-config',[PolicyController::class,'updateConfig']);
        Route::get('/{dataId}/reinsurance',[PolicyController::class,'reinsurance']);
        Route::patch('/{dataId}/update-reinsurance',[PolicyController::class,'updateReinsurance']);
        Route::patch('/{dataId}/approve',[PolicyController::class,'approve']);
        Route::patch('/{dataId}/endorse',[PolicyController::class,'endorse']);
    });
    Route::resource('/policies',PolicyController::class);
    Route::prefix('/policy')->group(function () {
        Route::patch('/{id}/approve', [PolicyController::class, 'approve']);
        Route::patch('/{id}/accept', [PolicyController::class, 'accept']);
        Route::get('/{id}/export/insured-persons', [PolicyController::class, 'exportInsuredPerson']);
        Route::get('/{id}/download/{lang}', [PolicyController::class, 'download']);
        Route::get('/{id}/download-invoice/{withSignature?}', [PolicyController::class, 'downloadInvoice']);
        Route::get('/{id}/download-certificate', [PolicyController::class, 'downloadCertificate']);
    });
    Route::post('/insured-persons/delete-multi',[InsuredPersonController::class,'deleteMulti']);
    Route::resource('/insured-persons',InsuredPersonController::class);

    Route::prefix('endorsements')->group(function(){
        Route::get('/{dataId}/info',[EndorsementController::class,'info']);
    });
    Route::resource('/endorsements',EndorsementController::class);
});
