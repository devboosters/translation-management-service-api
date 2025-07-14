<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedTranslations extends Command
{
    protected $signature = 'translations:seed
                            {--performance : Seed large dataset for performance testing}
                            {--users : Seed only API users}';

    protected $description = 'Seed translations and API users for testing';

    public function handle()
    {
        if ($this->option('users')) {
            $this->info('Seeding API users...');
            $this->call('db:seed', ['--class' => 'ApiUserSeeder']);
            $this->info('API users seeded successfully!');
            return;
        }

        if ($this->option('performance')) {
            $this->info('Seeding 100,000 translations for performance testing...');
            $this->call('db:seed', ['--class' => 'PerformanceTranslationSeeder']);
        } else {
            $this->info('Seeding regular dataset (100 translations + API user)...');
            $this->call('db:seed', ['--class' => 'DatabaseSeeder']);
        }

        $this->info('Done!');
        $this->line('Test API user credentials:');
        $this->line('Email: test-api-user@example.com');
        $this->line('Password: password');
        $this->line(
            'API Token: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG
9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMn0.KMUFsIDTnFmyG3nMiGM6H9FNFUROf3wh7SmqJp-QV30'
        );
    }
}
