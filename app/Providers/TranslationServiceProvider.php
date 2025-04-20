<?php

namespace App\Providers;

use App\Interfaces\TranslationRepositoryInterface;
use App\Repositories\TranslationRepository;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TranslationRepositoryInterface::class,
            TranslationRepository::class
        );
    }
}