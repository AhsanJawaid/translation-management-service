<?php

namespace Tests\Unit;

use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationGroup;
use App\Models\TranslationKey;
use App\Models\TranslationTag;
use App\Repositories\TranslationRepository;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    private TranslationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new TranslationRepository();
        $this->service = new TranslationService($repository);

        Language::factory()->create(['code' => 'en']);
    }

    public function test_create_translation(): void
    {
        $data = [
            'key' => 'test.key',
            'group' => 'test',
            'language_code' => 'en',
            'value' => 'Test value',
            'tags' => ['web', 'mobile'],
        ];

        $result = $this->service->createTranslation($data);

        $this->assertEquals('test.key', $result['key']);
        $this->assertEquals('test', $result['group']);
        $this->assertEquals('en', $result['language_code']);
        $this->assertEquals('Test value', $result['value']);
        $this->assertEquals(['web', 'mobile'], $result['tags']);
    }

    public function test_get_translation(): void
    {
        $translation = Translation::factory()->create();

        $result = $this->service->getTranslation($translation->id);

        $this->assertEquals($translation->id, $result['id']);
    }

    public function test_update_translation(): void
    {
        $translation = Translation::factory()->create();
        $newKey = TranslationKey::factory()->create(['key' => 'updated.key']);

        $result = $this->service->updateTranslation($translation->id, [
            'key' => 'updated.key',
            'value' => 'Updated value',
        ]);

        $this->assertEquals('updated.key', $result['key']);
        $this->assertEquals('Updated value', $result['value']);
    }

    public function test_search_translations(): void
    {
        $key = TranslationKey::factory()->create(['key' => 'unique.search.key']);
        Translation::factory()->create(['translation_key_id' => $key->id]);

        $result = $this->service->searchTranslations('unique.search.key');

        $this->assertEquals(1, $result->total());
    }

    public function test_export_translations(): void
    {
        $group = TranslationGroup::factory()->create(['name' => 'auth']);
        $key = TranslationKey::factory()->create(['key' => 'login.title']);
        Translation::factory()->create([
            'translation_key_id' => $key->id,
            'translation_group_id' => $group->id,
            'language_code' => 'en',
            'value' => 'Login'
        ]);

        $result = $this->service->exportTranslations('en');

        $this->assertEquals(['auth' => ['login.title' => 'Login']], $result);
    }
}