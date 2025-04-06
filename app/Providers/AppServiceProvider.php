<?php

namespace App\Providers;

use App\Repositories\Function\FncRepository;
use App\Repositories\Function\FncRepositoryContract;
use App\Repositories\Function\SmFncRepository;
use App\Repositories\SecurityManagement\OrganizationInterface;
use App\Repositories\SecurityManagement\OrganizationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('AuthLoginView', fn ($app) => 'security.login');
        $this->app->bind(OrganizationInterface::class, OrganizationRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(FncRepositoryContract::class, function ($app) {

            return in_array(request('app_code'), ['PORTAL', 'QUEUE', 'PGI']) ? $app->make(SmFncRepository::class) : $app->make(FncRepository::class);
        });
    }
}
