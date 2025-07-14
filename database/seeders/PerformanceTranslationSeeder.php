<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class PerformanceTranslationSeeder extends Seeder
{
    public function run()
    {
        // Large amount of translations for performance testing
        $batchSize = 1000;
        $totalRecords = 100000;
        $data = [];

        for ($i = 1; $i <= $totalRecords; $i++) {
            $data[] = [
                'group' => 'performance_group_' . rand(1, 10),
                'key' => 'performance_key_' . $i,
                'value' => 'Performance Translation value ' . $i,
                'locale' => [
                    'en',
                    'fr',
                    'es',
                    'af',
                    'am',
                    'ar',
                    'arn',
                    'as',
                    'az',
                    'ba',
                    'be',
                    'bg',
                    'bn',
                    'bo',
                    'br',
                    'bs'
                ][rand(0, 2)],
                'tag' => rand(0, 1) ? ['web', 'mobile', 'desktop', 'tablet'][rand(0, 2)] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($i % $batchSize === 0) {
                Translation::insert($data);
                $data = [];
            }
        }
    }
}
