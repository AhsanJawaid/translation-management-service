<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationGroup;
use App\Models\TranslationKey;
use App\Models\TranslationTag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create languages
        $languages = ['en', 'fr', 'es', 'de', 'it'];
        foreach ($languages as $code) {
            Language::create([
                'code' => $code,
                'name' => ucfirst($code),
                'is_active' => true
            ]);
        }

        // Create tags
        $tags = ['web', 'mobile', 'desktop', 'admin', 'public'];
        foreach ($tags as $tag) {
            TranslationTag::create(['name' => $tag]);
        }

        // Create groups
        $groups = ['auth', 'validation', 'products', 'users', 'settings'];
        foreach ($groups as $group) {
            TranslationGroup::create(['name' => $group]);
        }

        // Create 100k+ translations
        $translationKeys = TranslationKey::factory()
            ->count(10000)
            ->hasAttached(
                TranslationTag::inRandomOrder()->limit(rand(1, 3))->get()
            )
            ->create();

        foreach ($translationKeys as $key) {
            foreach ($languages as $language) {
                Translation::factory()->create([
                    'translation_key_id' => $key->id,
                    'translation_group_id' => TranslationGroup::inRandomOrder()->first()->id,
                    'language_code' => $language,
                ]);
            }
        }
    }
}