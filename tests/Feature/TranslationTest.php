<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationGroup;
use App\Models\TranslationKey;
use App\Models\TranslationTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;

        Language::factory()->create(['code' => 'en']);
        Language::factory()->create(['code' => 'fr']);
    }

    public function test_can_create_translation(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/translations', [
            'key' => 'test.key',
            'group' => 'test',
            'language_code' => 'en',
            'value' => 'Test value',
            'tags' => ['web', 'mobile'],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'key', 'group', 'language_code', 'value', 'tags'
            ]);
    }

    public function test_can_list_translations(): void
    {
        Translation::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translations');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_can_filter_translations_by_tag(): void
    {
        $tag = TranslationTag::factory()->create(['name' => 'web']);
        $key = TranslationKey::factory()->create();
        $key->tags()->attach($tag);
        Translation::factory()->create(['translation_key_id' => $key->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translations?tag=web');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_search_translations(): void
    {
        $key = TranslationKey::factory()->create(['key' => 'unique.search.key']);
        Translation::factory()->create(['translation_key_id' => $key->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translations/search?query=unique.search.key');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_export_translations(): void
    {
        $group = TranslationGroup::factory()->create(['name' => 'auth']);
        $key = TranslationKey::factory()->create(['key' => 'login.title']);
        Translation::factory()->create([
            'translation_key_id' => $key->id,
            'translation_group_id' => $group->id,
            'language_code' => 'en',
            'value' => 'Login'
        ]);

        $response = $this->getJson('/api/translations/export?locale=en&group=auth');

        $response->assertStatus(200)
            ->assertJson([
                'auth' => [
                    'login.title' => 'Login'
                ]
            ]);
    }

    public function test_export_performance(): void
    {
        Translation::factory()->count(1000)->create();

        $start = microtime(true);
        $response = $this->getJson('/api/translations/export?locale=en');
        $end = microtime(true);

        $response->assertStatus(200);
        $this->assertLessThan(0.5, $end - $start); // Less than 500ms
    }
}