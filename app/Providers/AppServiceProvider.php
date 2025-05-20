<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensCan(['company' => 'Access company endpoints', 'candidate' => 'Access candidate endpoints']);

        Password::defaults(function () {
            $rule = Password::min(8);
            return $this->app->isProduction()
                        ? $rule->letters()->mixedCase()->numbers()->symbols()->uncompromised()
                        : $rule;
        });
    }
}
