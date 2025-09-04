<?php

namespace App\Providers;

use App\Http\Requests\LoginRequest as CustomLoginRequest;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

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
        $this->app->bind(FortifyLoginRequest::class, CustomLoginRequest::class);
    }
}
