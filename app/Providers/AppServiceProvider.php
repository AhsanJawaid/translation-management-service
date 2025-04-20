<?php

namespace App\Providers;

use App\Interfaces\TranslationRepositoryInterface;
use App\Repositories\TranslationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TranslationRepositoryInterface::class,
            TranslationRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
