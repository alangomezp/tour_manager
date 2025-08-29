<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use App\BusinessLogic\services\definition\IUserService;
use App\BusinessLogic\services\implementation\UserService;
use App\Models\Tour;
use App\Models\User;
use App\Policies\TourPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Dependendy Injection
        $this->app->scoped(IUserService::class, UserService::class);

        //Gates
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Tour::class, TourPolicy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
