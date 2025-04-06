<?php

use App\Models\RefEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SecurityManagement\BranchController;
use App\Http\Controllers\SecurityManagement\ApplicationController;
use App\Http\Controllers\SecurityManagement\OrganizationController;
use App\Http\Controllers\SecurityManagement\FunctionController;
use App\Http\Controllers\SecurityManagement\RoleController;
use App\Http\Controllers\SecurityManagement\GroupController;
use App\Http\Controllers\SecurityManagement\UserController;
use App\Models\SecurityManagement\User;
use App\Models\User1;

// Route::middleware('authenticated:security')->group(function () {
Route::group(['middleware' => 'authenticated:security'], function () {
  Route::get('status', function() {
    return RefEnum::smStatuses();
  });
  
  Route::apiResource('organizations', OrganizationController::class, [
    'as' => 'sm'
  ])->except('destroy');

  Route::controller(BranchController::class)->prefix('branches')->group(function () {
    Route::get('/list-organization', 'listOrganization');
  });
  Route::apiResource('branches', BranchController::class, [
    'as' => 'sm'
  ])->except('destroy');

  Route::apiResource('applications', ApplicationController::class, [
    'as' => 'sm'
  ])->except('destroy');

  Route::controller(FunctionController::class)->prefix('functions')->group(function () {
    Route::get('/list-application', 'listApplication');
    Route::get('/list-permission', function() {
      return RefEnum::smPermissions();
    });
  });
  Route::apiResource('functions', FunctionController::class, [
    'as' => 'sm'
  ])->except('destroy');

  Route::controller(RoleController::class)->prefix('roles')->group(function () {
    Route::get('/list-permissions', 'listPermissions');
  });
  Route::apiResource('roles', RoleController::class, [
    'as' => 'sm'
  ])->except('destroy');

  Route::controller(GroupController::class)->prefix('groups')->group(function () {
    Route::get('/list-role', 'listRole');
  });
  Route::apiResource('groups', GroupController::class, [
    'as' => 'sm'
  ])->except('destroy');

  Route::controller(UserController::class)->prefix('users')->group(function () {
    Route::get('/list-organization', 'listOrganization');
    Route::get('/list-group', 'listGroup');
    Route::get('/list-role', 'listRole');
    Route::get('/list-permission', 'listPermission');
    Route::get('/list-authenticator', 'listAuthenticator');
    Route::get('/list-branch-by-org', 'listBranchesByOrg');
    Route::get('/{user}/actions', 'actions');
  });
  Route::apiResource('users', UserController::class, [
    'as' => 'sm'
  ])->except('destroy');
});