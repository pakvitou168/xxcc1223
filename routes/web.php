<?php

use App\Http\Controllers\CustomerManagement\CustomerProfileController;
use App\Http\Controllers\Renewal\RenewalController;
use App\Http\Controllers\SecurityManagement\SMController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Claim\PayeeController;
use App\Http\Controllers\Cover\CoverController;
use App\Http\Controllers\Claim\ProcessController;
use App\Http\Controllers\Policy\PolicyController;
use App\Http\Controllers\Claim\RecoveryController;
use App\Http\Controllers\Claim\RegisterController;
use App\Http\Controllers\Claim\Report\ClaimReportController;
use App\Http\Controllers\Claim\ServiceProviderController;
use App\Http\Controllers\Claim\ThirdPartyController;
use App\Http\Controllers\ProductConfiguration\ExchangeRate\ExchangeRateController;
use App\Http\Controllers\ProductConfiguration\ExchangeRate\ExchangeRateServiceController;
use App\Http\Controllers\ProductConfiguration\NoClaimDiscount\NoClaimDiscountController;
use App\Http\Controllers\ProductConfiguration\NoClaimDiscount\NoClaimDiscountServiceController;
use App\Http\Controllers\ProductConfiguration\PA\ExtensionOptionController;
use App\Http\Controllers\ProductConfiguration\VehicleUsage\VehicleUsageController;
use App\Http\Controllers\ProductConfiguration\VehicleUsage\VehicleUsageServiceController;
use App\Http\Controllers\ReinsuranceConfig\ReinsuranceConfigController;
use App\Http\Controllers\ReinsuranceConfig\ReinsuranceController;
use App\Http\Controllers\ReinsuranceConfig\ReinsurancePartnerController;
use App\Http\Controllers\ReinsuranceConfig\ReinsurancePartnerGroupController;
use App\Http\Controllers\ReinsuranceConfig\ReinsurancePartnerServiceController;
use App\Http\Controllers\ReinsuranceConfig\ReinsuranceTypeController;
use App\Http\Controllers\ProductConfiguration\ProductConditionRating\ProductConditionRatingController;
use App\Http\Controllers\Renewal\SurchargeRuleController;
use App\Http\Controllers\Claim\CauseOfLossController;
use App\Http\Controllers\Cover\CoverServiceController;
use App\Http\Controllers\Claim\DriverLicenseController;
use App\Http\Controllers\Claim\PartialPaymentController;
use App\Http\Controllers\Policy\PolicyServiceController;
use App\Http\Controllers\Quotation\AutoDetailController;
use App\Http\Controllers\Claim\AdjusterCompanyController;
use App\Http\Controllers\Insurance\EndorsementController;
use App\Http\Controllers\Quotation\AutoServiceController;
use App\Http\Controllers\UserManagement\Role\RoleController;
use App\Http\Controllers\UserManagement\User\UserController;
use App\Http\Controllers\CustomerManagement\CountryController;
use App\Http\Controllers\UserManagement\Group\GroupController;
use App\Http\Controllers\CustomerManagement\CustomerController;
use App\Http\Controllers\Deductible\DeductibleDetailController;
use App\Http\Controllers\Reinsurance\ReinsuranceDataController;
use App\Http\Controllers\Insurance\EndorsementServiceController;
use App\Http\Controllers\ProductConfiguration\Make\MakeController;
use App\Http\Controllers\BankInformation\BankInformationController;
use App\Http\Controllers\UserManagement\Role\RoleServiceController;
use App\Http\Controllers\UserManagement\User\UserServiceController;
use App\Http\Controllers\ProductConfiguration\Model\ModelController;
use App\Http\Controllers\UserManagement\Functions\FunctionController;
use App\Http\Controllers\UserManagement\Group\GroupServiceController;
use App\Http\Controllers\BusinessManagement\BusinessChannelController;
use App\Http\Controllers\BusinessManagement\BusinessHandlerController;
use App\Http\Controllers\CustomerManagement\CustomerServiceController;
use App\Http\Controllers\PolicyWording\PolicyWordingVersionController;
use App\Http\Controllers\BusinessManagement\BusinessCategoryController;
use App\Http\Controllers\ProductConfiguration\Product\ProductController;
use App\Http\Controllers\ProductConfiguration\Model\ModelServiceController;
use App\Http\Controllers\UserManagement\Applications\ApplicationController;
use App\Http\Controllers\ProductConfiguration\Formula\CompFormulaController;
use App\Http\Controllers\UserManagement\Functions\FunctionServiceController;
use App\Http\Controllers\BusinessManagement\BusinessChannelServiceController;
use App\Http\Controllers\CustomerManagement\CustomerClassificationController;
use App\Http\Controllers\PolicyWording\PolicyWordingVersionServiceController;
use App\Http\Controllers\ProductConfiguration\AccessRule\AccessRuleController;
use App\Http\Controllers\ProductConfiguration\Deductible\DeductibleController;
use App\Http\Controllers\ProductConfiguration\Product\ProductServiceController;
use App\Http\Controllers\ProductConfiguration\ProductLine\ProductLineController;
use App\Http\Controllers\ProductConfiguration\CoverPackage\CoverPackageController;
use App\Http\Controllers\ProductConfiguration\Formula\CompFormulaServiceController;
use App\Http\Controllers\ProductConfiguration\AccessRule\AccessRuleServiceController;
use App\Http\Controllers\ProductConfiguration\Deductible\DeductibleServiceController;
use App\Http\Controllers\ProductConfiguration\CoverComponent\CoverComponentController;
use App\Http\Controllers\ProductConfiguration\ProductLine\ProductLineServiceController;
use App\Http\Controllers\ProductConfiguration\CoverPackage\CoverPackageServiceController;
use App\Http\Controllers\ProductConfiguration\ClauseMaintenance\ClauseMaintenanceController;
use App\Http\Controllers\ProductConfiguration\CompFrmExpression\CompFrmExpressionController;
use App\Http\Controllers\ProductConfiguration\ComponentFormulaElement\CompFrmElementController;
use App\Http\Controllers\ProductConfiguration\ClauseMaintenance\ClauseMaintenanceServiceController;
use App\Http\Controllers\ProductConfiguration\CompFrmExpression\CompFrmExpressionServiceController;
use App\Http\Controllers\ProductConfiguration\VehicleClassification\VehicleClassificationController;
use App\Http\Controllers\ProductConfiguration\ComponentFormulaElement\CompFrmElementServiceController;
use App\Http\Controllers\Quotation\AutoController;
use Plb\SecurityManagement\Http\Controllers\LoginController;

// Auth::routes();
Route::get('/migrate-sm', [SMController::class, 'migrate']);
// Route::group(['middleware' => 'auth'], function () {
Route::group(['middleware' => 'authenticated:security'], function () {
    Route::post('/auth/change-password', [SMController::class, 'changePassword']);
    Route::get('/logged-in-info', function () {
        return auth()->user();
    });

    // Block Customer Management
    Route::resource('/customers', CustomerController::class);
    Route::resource('/customer-profiles', CustomerProfileController::class);

    // End Block Customer Management

    // Block Select Category For Customer Management
    Route::get('/refenumforcustomer', [CustomerServiceController::class, 'refEnumForCustomer']);

    // Customer Classification
    Route::resource('/customer-classifications', CustomerClassificationController::class);

    // Country
    Route::resource('/countries', CountryController::class);

    // Role Management
    Route::resource('/roles', RoleController::class);
    Route::prefix('/role-service')->group(function () {
        Route::get('/list-app-codes', [RoleServiceController::class, 'listAppCodes']);
        Route::get('/list-permissions/{app_code}', [RoleServiceController::class, 'listPermissions']);
        Route::get('/list-statuses', [RoleServiceController::class, 'listStatuses']);
        Route::get('/roles/functions/{role}', [RoleServiceController::class, 'listFunctions']);
    });
    // End Role Management

    // block Group Management
    Route::resource('/groups', GroupController::class);
    Route::prefix('/group-service')->group(function () {
        Route::get('/get-group-permissions', [GroupServiceController::class, 'getGroupPermissions']);
    });
    // end block Group Management

    // Cover routes
    Route::resource('/covers', CoverController::class);
    Route::prefix('/cover-service')->group(function () {
        Route::get('/list-auto-products', [CoverServiceController::class, 'listAutoProducts']);
        Route::get('/list-auto-products-with-desc', [CoverServiceController::class, 'listAutoProductsWithDesc']);
    });

    // Makes Route
    Route::resource('/makes', MakeController::class);

    //PA Extension route
    Route::resource('/extension-options', ExtensionOptionController::class);
    //Cover Component
    Route::resource('cover-component', CoverComponentController::class);

    // Deductible
    Route::resource('/deductibles', DeductibleController::class);
    Route::get('/deductibles-lovs', [DeductibleController::class, 'getLovs']);
    Route::get('/deductibles-list-products/{product_line_code?}', [DeductibleController::class, 'listProductByProductLineCode']);
    Route::get('/deductibles-list-covers/{product_code?}', [DeductibleController::class, 'listCoversByProductCode']);
    Route::prefix('/deductible-service')->group(function () {
        Route::get('/list-products', [DeductibleServiceController::class, 'listProducts']);
        Route::get('/list-products-with-desc', [DeductibleServiceController::class, 'listAutoProductsWithDesc']);
        Route::get('/list-covers-by-product-code/{productCode}', [DeductibleServiceController::class, 'listCoversByProductCode']);
    });


    // Clause Maintenance
    Route::resource('/clause-maintenances', ClauseMaintenanceController::class);
    Route::prefix('/clause-maintenance-service')->group(function () {
        Route::get('/get-services', [ClauseMaintenanceServiceController::class, 'getServices']);
        Route::get('/get-clause-types', [ClauseMaintenanceServiceController::class, 'getClauseTypes']);
        Route::get('/clause-type/{productLine}', [ClauseMaintenanceServiceController::class, 'clauseType']);
    });

    // Formula
    Route::resource('/formula', CompFormulaController::class);
    Route::prefix('/formula-service')->group(function () {
        Route::get('/list-products', [CompFormulaServiceController::class, 'listProducts']);
        Route::get('/list-products-by-product-line-code/{productLineCode}', [CompFormulaServiceController::class, 'listProductsByProductLineCode']);
        Route::get('/list-covers-by-product-code/{productCode?}', [CompFormulaServiceController::class, 'listCoversByProductCode']);
        Route::get('/list-product-comp', [CompFormulaServiceController::class, 'listProductComponents']);
    });

    // Formula Element
    Route::resource('/comp_form_element', CompFrmElementController::class);
    Route::prefix('/formula-service')->group(function () {
        Route::get('/list-formula', [CompFrmElementServiceController::class, 'listFormula']);
        Route::get('/list-element-types', [CompFrmElementServiceController::class, 'listElementTypes']);
    });

    // Formula Expression
    Route::resource('/comp_form_expression', CompFrmExpressionController::class);
    Route::prefix('/component-form-expression-service')->group(function () {
        Route::get('/list-components', [CompFrmExpressionServiceController::class, 'listComponentExpression']);
    });

    /* model */
    Route::resource('/models', ModelController::class);
    Route::prefix('/model-service')->group(function () {
        Route::get('/list-products-with-desc', [ModelServiceController::class, 'listAutoProductsWithDesc']);
        Route::get('/get-model-services', [ModelServiceController::class, 'getModelServices']);
    });

    /* Vehicle Classification */
    Route::resource('/vehicle-classifications', VehicleClassificationController::class);

    /* Access Rules */
    Route::resource('/access-rules', AccessRuleController::class);
    Route::prefix('/access-rule-service')->group(function () {
        Route::get('/list-models-by-make-id/{make_id}', [AccessRuleServiceController::class, 'listModelsByMakeId']);
        Route::get('/get-services', [AccessRuleServiceController::class, 'getAccessRuleServices']);
    });

    /* No Claim Discount */
    Route::resource('/no-claim-discounts', NoClaimDiscountController::class);
    Route::prefix('/no-claim-discounts-service')->group(function () {
        Route::get('/list-ncd/{product_code}', [NoClaimDiscountServiceController::class, 'listNoClaimDiscounts']);
        Route::get('/list-products-with-desc', [NoClaimDiscountServiceController::class, 'listAutoProducts']);
    });

    /* Product Condition Rating */
    Route::prefix('/product-config')->group(function () {
        Route::get('/product-condition-rating/list-products', [ProductConditionRatingController::class, 'listProducts']);
        Route::resource('/product-condition-rating', ProductConditionRatingController::class);
    });

    /* Products */
    Route::resource('/products', ProductController::class);
    Route::prefix('/product-service')->group(function () {
        Route::get('/list-auto-product-groups', [ProductServiceController::class, 'listAutoProductGroups']);
        Route::get('/list-commercial-vehicle-types', [ProductServiceController::class, 'listCommercialVehicleTypes']);
    });

    // Product Lines Route
    Route::resource('/product-lines', ProductLineController::class);
    Route::prefix('/product-line-service')->group(function () {
        Route::get('/list-product-lines', [ProductLineServiceController::class, 'listProductLines']);
    });

    // Block User Management Routes
    // Block User
    Route::resource('/users', UserController::class);
    Route::prefix('/user-service')->group(function () {
        Route::get('/get-service', [UserServiceController::class, 'getService']);
        Route::get('/roles/{id}', [UserServiceController::class, 'getRole']);
        Route::get('/permission/{id}', [UserServiceController::class, 'getPermission']);
        Route::get('/branch/{id}', [UserServiceController::class, 'getBranch']);
    });
    // Block User

    // Block Application
    Route::resource('/applications', ApplicationController::class);

    /* Functions */
    Route::resource('functions', FunctionController::class);
    Route::prefix('/function-service')->group(function () {
        Route::get('/list-permissions', [FunctionServiceController::class, 'listPermissions']);
        Route::get('/list-statuses', [FunctionServiceController::class, 'listStatuses']);
    });
    // End Block User Management Routes

    // Auto routes
    Route::resource('/autos', AutoController::class);
    Route::prefix('/autos')->group(function () {
        Route::post('/{auto}/clone', [AutoController::class, 'clone']);
        Route::get('/{auto}/can-generate-new-version', [AutoController::class, 'canGenerateNewVersion']);
        Route::get('/show-detail/{id}', [AutoController::class, 'showDetail']);
        Route::post('/approve/{auto}', [AutoController::class, 'approve']);
        Route::post('/accept/{auto}', [AutoController::class, 'accept']);
        Route::post('/proceed-to-policy/{auto}', [AutoController::class, 'proceedToPolicy']);
        Route::put('/update-issue-date/{auto}', [AutoController::class, 'updateIssuedDate']);
        Route::put('/revise-approval-status/{auto}', [AutoController::class, 'reviseApprovalStatus']);
        Route::put('/revise-acceptance-status/{auto}', [AutoController::class, 'reviseAcceptanceStatus']);

        Route::get('/{auto}/get-policy-id', [AutoController::class, 'getPolicyId']);

        // Auto Endorsement
        Route::put('/save-endorsement-general/{auto}', [AutoController::class, 'saveEndorsementGeneral']);
        Route::put('/save-cancel-policy-endorsement/{endorsement}', [AutoController::class, 'saveCancelPolicyEndorsement']);
        Route::put('/save-cancel-policy-endorsement/all-vehicles/{endorsement}', [AutoController::class, 'saveCancelPolicyEndorsementAllVehicles']);

        // Auto Function
        Route::prefix('/functions')->group(function () {
            Route::get('/check-validation', [AutoController::class, 'checkValidationFunctions']);
            Route::post('/generate-quotation-no', [AutoController::class, 'generateQuotationNo']);
            Route::post('/generate-overall-premium', [AutoController::class, 'generateOverallPremium']);
            Route::post('/generate-overall-deductible', [AutoController::class, 'generateOverallDeductible']);
            Route::post('/generate-single-premium', [AutoController::class, 'generateSinglePremium']);
            Route::post('/delete-vehicles', [AutoController::class, 'deleteVehicles']);
            Route::post('/delete-vehicles-manually', [AutoController::class, 'deleteVehiclesManually']);
        });
    });

    // Auto Detail
    Route::resource('/auto-details', AutoDetailController::class);
    // Auto Detail Endorsement
    Route::prefix('/auto-details')->group(function () {
        Route::put('/update-general-endorsement/{auto_detail}', [AutoDetailController::class, 'updateGeneralEndorsement']);
        Route::post('/save-endorsement-new-vehicle/{auto}', [AutoDetailController::class, 'saveEndorsementNewVehicle']);
        Route::put('/update-endorsement-vehicle/{auto}/{auto_detail}', [AutoDetailController::class, 'updateEndorsementVehicle']);
        Route::put('/delete-vehicle-endorsement/{id}', [AutoDetailController::class, 'deleteVehicleEndorsement']);
        Route::get('/show-endorsement-deleted-vehicles/{detail_id}', [AutoDetailController::class, 'showEndorsementDeletedVehicle']);
    });

    // Auto Service
    Route::prefix('/auto-service')->group(function () {
        Route::get('/get-services', [AutoServiceController::class, 'getServices']);
        Route::get('/list-customers-by-type/{type}', [AutoServiceController::class, 'listCustomersByType']);
        Route::get('/list-business-channels-by-category/{businessCategory}', [AutoServiceController::class, 'listBusinessChannelsByCategory']);
        Route::get('/find-business-channel/{businessCode}', [AutoServiceController::class, 'findBusinessChannel']);
        Route::post('/get-insured-names/{lang}', [AutoServiceController::class, 'getInsuredNames']);
        Route::get('/get-deductible-details/{auto}', [AutoServiceController::class, 'getDeductibleDetails']);
        Route::get('/get-product-code/{auto}', [AutoServiceController::class, 'getProductCode']);
        Route::get('/quotation/get-document-no/{masterDataId}', [AutoServiceController::class, 'getQuotationDocumentNo']);
        Route::get('/get-product-specification/{product_code}', [AutoServiceController::class, 'getProductSpecification']);
        Route::get('/list-policy-wording-version-by-product-code/{product_code}', [AutoServiceController::class, 'listPolicyWordingVersionByProductCode']);
        Route::get('/get-total-premium/{auto}', [AutoServiceController::class, 'getTotalPremium']);

        // Auto Service Endorsement
        Route::get('/get-default-endorsement-e-date/{auto}', [AutoServiceController::class, 'getDefaultEndorsementEffectiveDate']);

        // Auto Service Get Data For Adding Vehicles
        Route::get('/list-makes-by-product/{productCode}', [AutoServiceController::class, 'listMakesByProduct']);
        Route::get('/list-models-by-product-and-make/{productCode}/{makeId}', [AutoServiceController::class, 'listModelsByProductAndMake']);
        Route::get('/list-vehicle-usage-by-product-code/{product_code}', [AutoServiceController::class, 'listVehicleUsageByProductCode']);
        Route::get('/get-default-surcharge/{product_code}', [AutoServiceController::class, 'getDefaultSurcharge']);
        Route::get('/get-default-discount/{product_code}', [AutoServiceController::class, 'getDefaultDiscount']);
        Route::get('/get-default-ncd/{product_code}', [AutoServiceController::class, 'getDefaultNCD']);
        Route::get('/list-product-covers/{product_code}', [AutoServiceController::class, 'listProductCovers']);
        Route::get('/list-product-mandatory-covers/{product_code}', [AutoServiceController::class, 'listProductMandatoryCovers']);
        Route::get('/list-cover-packages/{product_code}', [AutoServiceController::class, 'listCoverPackages']);
        Route::get('/list-remain-covers/{package_id}/{product_code}', [AutoServiceController::class, 'listRemainCoversFromPackage']);
        Route::post('/vehicle-enums', [AutoServiceController::class, 'vehicleEnums']);

        //Auto Service Export Reports
        Route::prefix('/export-quotation')->group(function () {
            Route::get('/from{issue_date_from?}/to{issue_date_to?}', [AutoServiceController::class, 'exportQuotation']);
            Route::get('/from/to{issue_date_to?}', [AutoServiceController::class, 'exportQuotation']);
        });
        Route::get('/{auto}/download-quotation/{lang}', [AutoServiceController::class, 'downloadQuotation']);
        Route::get('/{id}/export-vehicles/{document_no}', [AutoServiceController::class, 'exportVehicles']);

        //Auto Service Upload Vehicle
        Route::put('/upload-vehicle-template', [AutoServiceController::class, 'uploadVehicleTemplate']);
        Route::prefix('/vehicle-upload')->group(function () {
            Route::post('/list-makes-by-product/{productCode}', [AutoServiceController::class, 'listMakesVehicleUploadByProduct']);
            Route::post('/list-models-by-product-and-make/{productCode}', [AutoServiceController::class, 'listModelsVehicleUploadByProductAndMake']);
        });
        Route::post('/file-vehicle-import', [AutoServiceController::class, 'fileVehicleImport']);
        Route::post('/clone-auto-temp-to-auto-detail', [AutoServiceController::class, 'saveToAutoDetail']);
        Route::delete('/delete-auto-temp-from-detail', [AutoServiceController::class, 'deleteVehicleUpload']);
    });

    // Business Category
    Route::resource('/business-categories', BusinessCategoryController::class);

    // Business Handler
    Route::resource('/business-handlers', BusinessHandlerController::class);

    // Business Channel
    Route::resource('/business-channels', BusinessChannelController::class);
    Route::prefix('/business-channels-service')->group(function () {
        Route::get('/list-business-categories', [BusinessChannelServiceController::class, 'listBusinessCategories']);
        Route::get('/list-sale-channels', [BusinessChannelServiceController::class, 'listSaleChannels']);
        Route::get('/list-business-handlers', [BusinessChannelServiceController::class, 'listBusinessHandlers']);
        Route::get('/list-parents', [BusinessChannelServiceController::class, 'listParents']);
    });

    // Policy Wording
    Route::resource('/policy-wording-versions', PolicyWordingVersionController::class);
    Route::prefix('/policy-wording-versions-service')->group(function () {
        Route::get('/list-product-lines', [PolicyWordingVersionServiceController::class, 'listProductLines']);
        Route::get('/list-products-by-product-line/{id}', [PolicyWordingVersionServiceController::class, 'listProductsByProductLine']);
        Route::get('/list-products-by-product-line-code-with-desc/{productLineCode}', [PolicyWordingVersionServiceController::class, 'listProductsWithDescByProductLine']);
    });

    // Cover Packages
    Route::resource('/cover-packages', CoverPackageController::class);
    Route::prefix('/cover-packages-service')->group(function () {
        Route::get('/list-product-covers/{product_code}', [CoverPackageServiceController::class, 'listProductCovers']);
        Route::get('/list-product-mandatory-covers/{product_code}', [CoverPackageServiceController::class, 'listProductMandatoryCovers']);
    });

    // Policies
    Route::apiResource('/api/policies', PolicyController::class)->except([
        'store'
    ]);
    Route::prefix('/api/policies')->group(function () {
        Route::post('/revise/{policy}', [PolicyController::class, 'revise']);
        Route::post('/approve/{policy}', [PolicyController::class, 'approve']);
        Route::post('/generate-auto-endorsement/{policy}', [PolicyController::class, 'generateAutoEndorsement']);
        Route::get('/check-can-generate-auto-endorsement/{policy}', [PolicyServiceController::class, 'checkCanGenerateEndorsement']);
        Route::post('/generate-auto-invoice', [PolicyController::class, 'generateAutoInvoice']);
        Route::post('/generate-claim', [PolicyController::class, 'generateClaim']);
    });
    // Policy Services
    Route::prefix('/policy-service')->group(function () {
        Route::get('/list-business-types', [PolicyServiceController::class, 'listBusinessTypes']);
        Route::get('/list-policy-types', [PolicyServiceController::class, 'listPolicyTypes']);
        Route::get('/is-policy-configuration-completed/{policy}', [PolicyServiceController::class, 'isPolicyConfigurationCompleted']);
        Route::put('/update-submit-status/{policy}', [PolicyServiceController::class, 'updateSubmitStatus']);
        Route::get('/get-signature/{policy}', [PolicyServiceController::class, 'getSignatureByPolicy']);

        // Policy Service Commission
        Route::get('/generate-commission-data/{policy_id}', [PolicyServiceController::class, 'generateCommissionData']);
        Route::get('/get-commission-data/{policy_id}', [PolicyServiceController::class, 'getCommissionData']);
        Route::get('/get-commission-data-by-vehicle/{detail_id}', [PolicyServiceController::class, 'getCommissionDataByVehicle']);
        Route::get('/get-business-name-by-business-code/{business_code}', [PolicyServiceController::class, 'getBusinessNameByBusinessCode']);
        Route::get('/is-commission-data-available/{policy_id}', [PolicyServiceController::class, 'isCommissionDataAvailable']);
        Route::post('/update-commission-data/{policy_id}', [PolicyServiceController::class, 'updateCommissionData']);

        // Policy Service Reinsurance
        Route::get('/generate-reinsurance-share/{policy_id}', [PolicyServiceController::class, 'generateReinsuranceShare']);
        Route::get('/generate-reinsurance-data/{policy_id}', [PolicyServiceController::class, 'generateReinsuranceData']);
        Route::get('/get-reinsurance-data/{policy_id}', [PolicyServiceController::class, 'getReinsuranceData']);
        Route::get('/check-if-share-under-limit/{policy_id}/{auto_detail_id}', [PolicyServiceController::class, 'checkIfShareUnderLimit']);
        Route::get('/is-policy-reinsurance-completed/{policy}', [PolicyServiceController::class, 'isPolicyReinsuranceCompleted']);
        Route::get('/list-treaty-codes', [PolicyServiceController::class, 'listTreatyCodes']);
        Route::get('/list-partner-groups', [PolicyServiceController::class, 'listPartnerGroupCodes']);

        // Policy Service Export
        Route::get('export-reinsurance/{id}/{product_code}', [PolicyServiceController::class, 'exportReinsurance']);
        Route::prefix('/export-policy')->group(function () {
            Route::get('/from{issue_date_from?}/to{issue_date_to?}', [PolicyServiceController::class, 'exportPoilcy']);
            Route::get('/from/to{issue_date_to?}', [PolicyServiceController::class, 'exportPoilcy']);
        });
        Route::get('/{id}/download-invoice/{withSignature?}', [PolicyServiceController::class, 'downloadInvoice']);
        Route::get('/{id}/download-auto-certificate', [PolicyServiceController::class, 'downloadAutoCertificate']);
        Route::get('/{policy}/download-policy-schedule/{lang}', [PolicyServiceController::class, 'downloadPolicySchedule']);

        // Policy Service Endorsement
        Route::get('/list-auto-endorsement-types', [PolicyServiceController::class, 'listAutoEndorsementTypes']);
    });

    //setup surcharge
    Route::apiResource('/api/surcharge-rules', SurchargeRuleController::class);
    // Endorsement
    Route::apiResource('/api/endorsements', EndorsementController::class)->except([
        'store'
    ]);
    Route::prefix('/api/endorsements')->group(function () {
        Route::post('/approve/{endorsement}', [EndorsementController::class, 'approve']);
        Route::post('/revise/{endorsement}', [EndorsementController::class, 'revise']);
        Route::post('/generate-auto-endorsement/{endorsement}', [EndorsementController::class, 'generateAutoEndorsement']);
        Route::get('/show-detail/{endorsement}', [EndorsementController::class, 'showDetail']);
        Route::get('/get-premium/{endorsement}/{endorsement_stage}/{raw_number?}', [EndorsementController::class, 'getPremium']);
        Route::get('/get-reinsurance-data/{endorsement}', [EndorsementController::class, 'getReinsuranceData']);
        Route::get('/list-cancel-vehicles/{endorsement}', [EndorsementController::class, 'listCancelVehicles']);
        Route::post('/generate-auto-credit-note', [EndorsementController::class, 'generateAutoCreditNote']);
    });

    // Endorsement Services
    Route::prefix('/endorsement-service')->group(function () {
        Route::get('/list-refund-type-options', [EndorsementServiceController::class, 'listRefundTypeOptions']);
        Route::get('/show-policy-cancellation-tab/{endorsement}', [EndorsementServiceController::class, 'showPolicyCancellationTab']);
        Route::get('/generate-reinsurance-share/{endorsement}', [EndorsementServiceController::class, 'generateReinsuranceShare']);
        Route::get('/generate-reinsurance-data/{endorsement}', [EndorsementServiceController::class, 'generateReinsuranceData']);
        Route::get('/check-can-export-vehicle-list-for-all/{endorsement}', [EndorsementServiceController::class, 'checkCanExportVehicleListForAll']);
        Route::put('/update-submit-status/{endorsement}', [EndorsementServiceController::class, 'updateSubmitStatus']);
        Route::get('/is-endorsement-reinsurance-completed/{endorsement}', [EndorsementServiceController::class, 'isEndorsementReinsuranceCompleted']);
        Route::get('/get-commission-data/{endorsement}', [EndorsementServiceController::class, 'getCommissionData']);
        Route::get('/is-commission-data-available/{endorsement}', [EndorsementServiceController::class, 'isCommissionDataAvailable']);
        Route::get('/has-endorsement-vehicles/{endorsement}', [EndorsementServiceController::class, 'hasEndorsedVehicles']);
        Route::get('/{endorsement}/check-has-new-vehicle', [EndorsementServiceController::class, 'checkEndorsementHasNewVehicle']);
        Route::get('/get-valid-endorsement-date-period/{id}', [EndorsementServiceController::class, 'getValidEndorsementDatePeriod']);

        // Endorsement Exports
        Route::get('/{id}/download-credit-note/{withSignature?}', [EndorsementServiceController::class, 'downloadCreditNote']);
        Route::get('/{id}/download-invoice/{withSignature?}', [EndorsementServiceController::class, 'downloadInvoice']);
        Route::get('/{id}/download-auto-certificate', [EndorsementServiceController::class, 'downloadAutoCertificate']);
        Route::get('/{endorsement}/download-endorsement', [EndorsementServiceController::class, 'downloadEndorsement']);
        Route::get('/{id}/export-vehicles/{document_no}/{endoorsement_type}', [EndorsementServiceController::class, 'exportVehicles']);
        Route::get('/{id}/export-vehicle-lists-for-all', [EndorsementServiceController::class, 'exportVehicleListsForAll']);
        Route::prefix('/export-excel')->group(function () {
            Route::get('/from{issue_date_from?}/to{issue_date_to?}', [EndorsementServiceController::class, 'exportEndorsements']);
            Route::get('/from/to{issue_date_to?}', [EndorsementServiceController::class, 'exportEndorsements']);
        });
    });

    // Bank Information
    Route::resource('/api/bank-informations', BankInformationController::class);

    // Reinsurance Data
    Route::prefix('/reinsurance-data')->group(function () {
        Route::post('/stores', [ReinsuranceDataController::class, 'stores']);
        Route::get('/{policy_id}/get-sum/{detail_id}/{endorsementNo?}', [ReinsuranceDataController::class, 'getSum']);
    });

    // Deductible
    Route::prefix('/deductible-details')->group(function () {
        Route::put('/{deductible_detail}', [DeductibleDetailController::class, 'update']);
        Route::post('/updates', [DeductibleDetailController::class, 'updates']);
    });

    // Reinsurance
    Route::resource('/reinsurances', ReinsuranceController::class);

    // Reinsurance Type
    Route::resource('/reinsurance-types', ReinsuranceTypeController::class);

    // Reinsurance Partner Group
    Route::resource('/reinsurance-partner-groups', ReinsurancePartnerGroupController::class);

    // Reinsurance Partner
    Route::resource('/reinsurance-partners', ReinsurancePartnerController::class);
    Route::prefix('/reinsurance-partners-service')->group(function () {
        Route::get('/get-group-code', [ReinsurancePartnerServiceController::class, 'getReinsuranceGroupCodeList']);
    });

    // Reinsurance Config
    Route::resource('/reinsurance-configs', ReinsuranceConfigController::class);
    Route::prefix('/reinsurance-config')->group(function () {
        Route::get('/products-by-product-line/{id}', [ReinsuranceConfigController::class, 'productsByProductLine']);
        Route::get('/parent-by-product-code/{code}', [ReinsuranceConfigController::class, 'parentByProductCode']);
        Route::get('/3re', [ReinsuranceConfigController::class, '_3re']);
        Route::get('/get-default-reinsurance-config/{code}', [ReinsuranceConfigController::class, 'getDefaultReinsuranceConfig']);
    });

    // Exchange Rate
    Route::resource('/exchange-rates', ExchangeRateController::class);
    Route::prefix('/exchange-rate-service')->group(function () {
        Route::get('/branch-code', [ExchangeRateServiceController::class, 'getBranch']);
        Route::get('/currency', [ExchangeRateServiceController::class, 'getCurrency']);
        Route::get('/rate-type', [ExchangeRateServiceController::class, 'getRateType']);
        Route::get('/get-latest', [ExchangeRateServiceController::class, 'getLatestExchangeRate']);
        Route::post('/approve/{exchange_rate}', [ExchangeRateServiceController::class, 'approve']);
    });

    // Vehicle Usage
    Route::resource('/vehicle-usages', VehicleUsageController::class);
    Route::prefix('/vehicle-usages-service')->group(function () {
        Route::get('/list-auto-products', [VehicleUsageServiceController::class, 'listAutoProducts']);
    });

    // Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

    Route::apiResource('/claim-cause-of-losses', CauseOfLossController::class);

    Route::apiResource('/claim-driver-licenses', DriverLicenseController::class);

    Route::apiResource('/claim-payees', PayeeController::class);
    Route::get('/claim-payees-lovs', [PayeeController::class, 'getLovs']);

    Route::apiResource('/adjuster-companies', AdjusterCompanyController::class);

    Route::apiResource('/claim-third-parties', ThirdPartyController::class);

    Route::apiResource('/claim-service-providers', ServiceProviderController::class);

    Route::apiResource('/claim-registers', RegisterController::class);
    Route::controller(RegisterController::class)->prefix('/claim-registers')->group(function () {
        Route::post('/{id}/approve', 'approve');
        Route::post('/{id}/revise', 'revise');
        Route::get('/{id}/print/{lang}', 'print');
        Route::get('/{id}/detail', 'detail');
    });
    Route::get('/claim-register-lovs', [RegisterController::class, 'getLovs']);
    Route::get('/claim-register-list-vehicles/{data_id}', [RegisterController::class, 'listVehicles']);
    Route::get('/claim-register-list-covers/{detail_id}', [RegisterController::class, 'listCovers']);
    Route::get('/claim-register-list-deductible/{detail_id}', [RegisterController::class, 'getDeductibleDetail']);

    Route::apiResource('/claim-partial-payments', PartialPaymentController::class);
    Route::controller(PartialPaymentController::class)->prefix('/claim-partial-payments')->group(function () {
        Route::get('/{id}/detail', 'detail');
        Route::post('/{id}/approve', 'approve');
        Route::post('/{id}/revise', 'revise');
        Route::put('/{id}/save-payment-numbers', 'savePaymentNumbers');
        Route::get('/{id}/have-payment-numbers', 'havePaymentNumbers');
        Route::get('/{id}/print-cheque/{lang}', 'printCheque');
        Route::get('/{id}/print/{lang}', 'print');
    });
    Route::get('/claim-partial-vehicle/{detailId}', [PartialPaymentController::class, 'getVehicle']);
    Route::get('/claim-partial-payments-lovs', [PartialPaymentController::class, 'getLovs']);
    Route::get('/claim-partial-payments-list-cause-of-losses/{claim_no}', [PartialPaymentController::class, 'listCauseOfLosses']);

    Route::apiResource('/claim-processes', ProcessController::class);
    Route::controller(ProcessController::class)->prefix('/claim-processes')->group(function () {
        Route::get('/{id}/detail', 'detail');
        Route::post('/{id}/approve', 'approve');
        Route::post('/{id}/revise', 'revise');
        Route::get('/{id}/print/{lang}', 'print');
        Route::get('/{id}/print-cheque/{lang}', 'print');
        Route::get('/{id}/print-revision/{lang}', 'print');
        Route::put('/{id}/save-payment-numbers', 'savePaymentNumbers');
        Route::get('/{id}/have-payment-numbers', 'havePaymentNumbers');
        Route::post('/{id}/generate-recovery', 'generateRecovery');
        Route::get('/{id}/already-generated-recovery', 'alreadyGeneratedRecovery');
    });
    Route::get('/claim-processes-get-vehicle/{detailId}', [ProcessController::class, 'getVehicle']);
    Route::get('/claim-processes-lovs', [ProcessController::class, 'getLovs']);
    Route::get('/claim-processes-list-cause-of-losses/{claim_no}', [ProcessController::class, 'listCauseOfLosses']);
    Route::post('/claim-processes-preview-deductibles', [ProcessController::class, 'previewAndValidateDeductibles']);

    Route::apiResource('/claim-recoveries', RecoveryController::class)->except(['store']);
    Route::get('/claim-recoveries/{id}/print/{lang}', [RecoveryController::class, 'print']);

    Route::get('/claim-recoveries-lovs', [RecoveryController::class, 'getLovs']);
    Route::controller(RecoveryController::class)->prefix('/claim-recoveries')->group(function () {
        Route::post('/{id}/approve', 'approve');
    });

    // Export Claim Reports
    Route::prefix('/claim-report')->group(function () {
        Route::get('/type{report_type}/from{from_date?}/to{to_date?}', [ClaimReportController::class, 'exportClaim']);
        Route::get('/type{report_type}/from/to{to_date?}', [ClaimReportController::class, 'exportClaim']);
    });

    Route::get('/api/renewals', [RenewalController::class, 'index']);
    Route::get('/api/renewals/status-lovs', [RenewalController::class, 'listStatusLovs']);
    Route::get('/api/renewals/{id}', [RenewalController::class, 'show']);
    Route::post('/api/renewals/generate-renewal-list', [RenewalController::class, 'generateRenewalList']);
    Route::post('/api/renewals/auto-approve-no-claim-policies', [RenewalController::class, 'autoApproveNoClaimPolicies']);
    Route::post('/api/renewals/generate-renewed-policy/{id}', [RenewalController::class, 'generateRenewedPolicy']);
    Route::post('/api/renewals/can-generate-renewed-policy/{id}', [RenewalController::class, 'canGenerateRenewedPolicy']);
    Route::post('/api/renewals/generate-new-version/{id}', [RenewalController::class, 'generateNewVersion']);
    Route::post('/api/renewals/{id}/approve', [RenewalController::class, 'approve']);
    Route::post('/api/renewals/{id}/accept', [RenewalController::class, 'accept']);
    Route::post('/api/renewals/{id}/revise', [RenewalController::class, 'revise']);
    Route::post('/api/renewals/{id}/submit', [RenewalController::class, 'submit']);
    Route::get('/api/renewals/{id}/edit', [RenewalController::class, 'edit']);
    Route::post('/api/renewals/export', [RenewalController::class, 'export']);
    Route::get('renewals/{auto}/download-quotation/{lang}', [RenewalController::class, 'downloadQuotation']);
    Route::get('renewals/{id}/download/{lang}', [RenewalController::class, 'downloadRenewal']);
    Route::get('renewals/{id}/export-vehicles/{document_no}', [RenewalController::class, 'exportVehicles']);

    // <Authentication>
    Route::get('/logout', [LoginController::class, 'logout']);
    // </Authentication>
    Route::get('/{any}', function () {
        return view('app');
    })->where('any', '.*');
});
