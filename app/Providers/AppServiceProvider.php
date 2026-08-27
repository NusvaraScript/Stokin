<?php

namespace App\Providers;

use App\Modules\Auth\Contracts\AuthRepositoryInterface;
use App\Modules\Auth\Repositories\AuthRepository;
use App\Modules\Auth\Services\AuthService;
use App\Modules\User\Contracts\UserRepositoryInterface;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Services\UserService;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(AuthService::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensCan([
            'admin' => 'Akses Admin',
            'user' => 'Akses User',
            ]);
        Passport::enablePasswordGrant();
    }
}
