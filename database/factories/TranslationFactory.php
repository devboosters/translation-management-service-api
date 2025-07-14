<?php

namespace Database\Factories;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Translation::class;

    public function definition()
    {
        static $keyIndex = 0;

        return [
            'group' => $this->faker->word,
            'key' => 'translation_key_' . $keyIndex++,
            'value' => $this->faker->sentence,
            'locale' => $this->faker->randomElement(
                ['en', 'fr', 'es', 'af', 'am', 'ar', 'arn', 'as', 'az', 'ba', 'be', 'bg', 'bn', 'bo', 'br', 'bs']
            ),
            'tag' => $this->faker->randomElement(['web', 'mobile', 'desktop', 'tablet']),
        ];
    }

    /**
     * For performance Optimzation
     *
     * @return void
     */
    public function configure()
    {
        return $this->afterCreating(function (Translation $translation) {
            // Optional: Clear cache after creation
            if (app()->environment('local')) {
                \Cache::forget('translations.' . $translation->locale);
            }
        });
    }
}
