<?php

namespace App\Providers;

use App\Repositories\GameRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\GameRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the interface to the repository.
        $this->app->bind(GameRepositoryInterface::class, GameRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
