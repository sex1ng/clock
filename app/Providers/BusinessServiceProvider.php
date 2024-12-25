<?php

namespace App\Providers;

use App\Services\Business\DiaryService;
use App\Services\Business\ResponseService;
use App\Services\Business\TargetService;
use App\Services\Business\UserService;
use Illuminate\Support\ServiceProvider;

class BusinessServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(ResponseService::class, ResponseService::class);
        $this->app->singleton(UserService::class, UserService::class);
        $this->app->singleton(TargetService::class, TargetService::class);
        $this->app->singleton(DiaryService::class, DiaryService::class);
    }

}
