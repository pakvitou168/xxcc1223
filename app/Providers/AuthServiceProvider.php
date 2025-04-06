<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use ReflectionClass;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

        $this->registerPolicies();

        Gate::guessPolicyNamesUsing(function ($modelClass) {
            $class = new ReflectionClass($modelClass);
            $namespace = $class->getNamespaceName();
            $className = $class->getShortName();
            // Remove App/Models
            $namespaceArr = explode('\\', $namespace);
            unset($namespaceArr[0], $namespaceArr[1]);
            $path = implode('\\', $namespaceArr);

            if (!$path) return 'App\\Policies\\' . $className . 'Policy';

            return 'App\\Policies\\' . $path . '\\' . $className . 'Policy';
        });
    }
}
