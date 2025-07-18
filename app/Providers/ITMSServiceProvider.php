<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class ITMSServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register ITMS services
        $this->app->singleton('itms.helper', function ($app) {
            return new \App\Helpers\ITMSHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Gates for authorization
        $this->registerGates();

        // Load additional configuration
        $this->loadConfigurations();
    }

    /**
     * Register authorization gates.
     */
    private function registerGates(): void
    {
        // Employee gates
        Gate::define('employee.view', function (User $user) {
            return $user->hasPermission('employee.view') || $user->isAdmin();
        });

        Gate::define('employee.create', function (User $user) {
            return $user->hasPermission('employee.create') || $user->isAdmin();
        });

        Gate::define('employee.update', function (User $user) {
            return $user->hasPermission('employee.update') || $user->isAdmin();
        });

        Gate::define('employee.delete', function (User $user) {
            return $user->hasPermission('employee.delete') || $user->isAdmin();
        });

        // Asset gates
        Gate::define('asset.view', function (User $user) {
            return $user->hasPermission('asset.view') || $user->isAdmin();
        });

        Gate::define('asset.create', function (User $user) {
            return $user->hasPermission('asset.create') || $user->isAdmin();
        });

        Gate::define('asset.update', function (User $user) {
            return $user->hasPermission('asset.update') || $user->isAdmin();
        });

        Gate::define('asset.delete', function (User $user) {
            return $user->hasPermission('asset.delete') || $user->isAdmin();
        });

        // Incident gates
        Gate::define('incident.view', function (User $user) {
            return $user->hasPermission('incident.view') || $user->isAdmin();
        });

        Gate::define('incident.create', function (User $user) {
            return $user->hasPermission('incident.create') || $user->isAdmin();
        });

        Gate::define('incident.update', function (User $user) {
            return $user->hasPermission('incident.update') || $user->isAdmin();
        });

        Gate::define('incident.delete', function (User $user) {
            return $user->hasPermission('incident.delete') || $user->isAdmin();
        });

        // Service Request gates
        Gate::define('service_request.view', function (User $user) {
            return $user->hasPermission('service_request.view') || $user->isAdmin();
        });

        Gate::define('service_request.create', function (User $user) {
            return $user->hasPermission('service_request.create') || $user->isAdmin();
        });

        Gate::define('service_request.update', function (User $user) {
            return $user->hasPermission('service_request.update') || $user->isAdmin();
        });

        Gate::define('service_request.delete', function (User $user) {
            return $user->hasPermission('service_request.delete') || $user->isAdmin();
        });

        // Agreement gates
        Gate::define('agreement.view', function (User $user) {
            return $user->hasPermission('agreement.view') || $user->isAdmin();
        });

        Gate::define('agreement.create', function (User $user) {
            return $user->hasPermission('agreement.create') || $user->isAdmin();
        });

        Gate::define('agreement.update', function (User $user) {
            return $user->hasPermission('agreement.update') || $user->isAdmin();
        });

        Gate::define('agreement.delete', function (User $user) {
            return $user->hasPermission('agreement.delete') || $user->isAdmin();
        });
    }

    /**
     * Load additional configurations.
     */
    private function loadConfigurations(): void
    {
        // Set default timezone
        if (config('itms.system.timezone')) {
            date_default_timezone_set(config('itms.system.timezone'));
        }
    }
}
