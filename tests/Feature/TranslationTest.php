<?php

namespace Tests\Feature;

use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    protected static $testingAuth; // declaring auth variable for authentication while running test cases

    /**
     * Set up the test case for the fresh application.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Creating an authenticated user for the testing purposes to make it usable throughout the translations tests
        self::$testingAuth = $this->createAuthenticatedUser();
    }

    /**
     * Authenticating through $test_auth_token
     *
     * @return string[]
     */
    protected function authenticate()
    {
        //return ['Authorization' => 'Bearer test-api-token-ABCDEFGHIJKLMNOPQSTUVWXYZ-1234567890'];
        //return ['Authorization' => 'Bearer '. $test_auth_token];
        return ['Authorization' => 'Bearer ' . self::$testingAuth['token']];
    }


    public function test_create_translation()
    {
        $response = $this->postJson('/api/translations', [
            'group' => 'validation',
            'key' => 'required',
            'value' => 'This field is required',
            'locale' => 'en',
            'tag' => 'web'
        ], $this->authenticate());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'group',
                'key',
                'value',
                'locale',
                'tag'
            ]);
    }

    public function test_get_translation()
    {
        $translation = Translation::factory()->create();

        $response = $this->getJson("/api/translations/{$translation->id}", $this->authenticate());

        $response->assertStatus(200)
            ->assertJson([
                'id' => $translation->id,
                'key' => $translation->key
            ]);
    }

    public function test_update_translation()
    {
        $translation = Translation::factory()->create();

        $response = $this->putJson("/api/translations/{$translation->id}", [
            'value' => 'Updated Translation Value'
        ], $this->authenticate());

        $response->assertStatus(200)
            ->assertJson(['message' => 'Translation updated']);
    }

    public function test_delete_translation()
    {
        $translation = Translation::factory()->create();

        $response = $this->deleteJson("/api/translations/{$translation->id}", [], $this->authenticate());

        $response->assertStatus(200)
            ->assertJson(['message' => 'Translation deleted']);
    }

    public function test_search_translations()
    {
        Translation::factory()->create(
            [
                'group' => 'testing_case',
                'key' => 'testing_special_key_2',
                'value' => 'special testing value',
                'locale' => 'en',
                'tag' => 'desktop'
            ]
        );

        $response = $this->getJson('/api/translations/search?query=special', $this->authenticate());

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_export_translations()
    {
        Translation::factory()->create(
            [
                'group' => 'validation',
                'key' => 'required',
                'value' => 'This field is required',
                'locale' => 'en'
            ]
        );

        $response = $this->getJson('/api/translations/export?locale=en', $this->authenticate());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'en' => [
                    'validation' => [
                        'required'
                    ]
                ]
            ])
            ->assertJson([
                'en' => [
                    'validation' => [
                        'required' => 'This field is required'
                    ]
                ]
            ]);
    }

    public function test_performance()
    {
        Translation::factory()->count(5)->create();

        $start = microtime(true);
        $response = $this->getJson('/api/translations/search', $this->authenticate());
        $duration = (microtime(true) - $start) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(200, $duration, "The Response time should be less than 200ms");
    }
}
