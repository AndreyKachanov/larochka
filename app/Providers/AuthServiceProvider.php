<?php

namespace App\Providers;

use App\Entity\User\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->registerPermissions();
    }

    private function registerPermissions(): void
    {
        Gate::define('admin-panel', function (User $user) {
            return $user->isAdmin();
        });


        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('show-parser', function (User $user) {
            return $user->isAdmin();
        });
    }
}
