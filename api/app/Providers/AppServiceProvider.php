<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Les Gate sont simplement des fermetures qui déterminent si un utilisateur est autorisé à effectuer
        //  une action donnée.
        Gate::define('update-post', function (User $user) {
            return;
        });
    }
}
