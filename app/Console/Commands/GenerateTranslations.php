<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateTranslations extends Command
{
    protected $signature = 'translations:generate {--count=100000 : Number of translations to generate}';
    protected $description = 'Generate test translations for performance testing';

    public function handle(): int
    {
        $count = (int)$this->option('count');

        $this->info("Generating {$count} translations...");

        Artisan::call('migrate:fresh');
        Artisan::call('db:seed', ['--class' => 'DatabaseSeeder']);

        $this->info("Successfully generated {$count} translations.");

        return 0;
    }
}