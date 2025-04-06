<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\SecurityManagement\BranchInterface;
use App\Repositories\SecurityManagement\BranchRepository;
use App\Repositories\SecurityManagement\FunctionInterface;
use App\Repositories\SecurityManagement\FunctionRepository;
use App\Repositories\SecurityManagement\ApplicationInterface;
use App\Repositories\SecurityManagement\ApplicationRepository;
use App\Repositories\SecurityManagement\OrganizationInterface;
use App\Repositories\SecurityManagement\OrganizationRepository;
use App\Repositories\SecurityManagement\PermissionInterface;
use App\Repositories\SecurityManagement\PermissionRepository;
use App\Repositories\SecurityManagement\RoleInterface;
use App\Repositories\SecurityManagement\RoleRepository;
use App\Repositories\SecurityManagement\GroupInterface;
use App\Repositories\SecurityManagement\GroupRepository;
use App\Repositories\SecurityManagement\UserInterface;
use App\Repositories\SecurityManagement\UserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OrganizationInterface::class, OrganizationRepository::class);
        $this->app->bind(BranchInterface::class, BranchRepository::class);
        $this->app->bind(ApplicationInterface::class, ApplicationRepository::class);
        $this->app->bind(FunctionInterface::class, FunctionRepository::class);
        $this->app->bind(PermissionInterface::class, PermissionRepository::class);
        $this->app->bind(RoleInterface::class, RoleRepository::class);
        $this->app->bind(GroupInterface::class, GroupRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
