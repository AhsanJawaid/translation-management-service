<?php

namespace App\Repositories;

use App\Interfaces\TranslationRepositoryInterface;
use App\Models\Translation;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class TranslationRepository implements TranslationRepositoryInterface
{
    public function getAllTranslations(array $filters = [], int $perPage = 10): Paginator
    {
        return Translation::with(['key', 'group', 'language'])
            ->when(isset($filters['key']), fn($query) => $query->whereHas('key', fn($q) => $q->where('key', 'like', "%{$filters['key']}%")))
            ->when(isset($filters['group']), fn($query) => $query->whereHas('group', fn($q) => $q->where('name', 'like', "%{$filters['group']}%")))
            ->when(isset($filters['language']), fn($query) => $query->where('language_code', $filters['language']))
            ->when(isset($filters['tag']), fn($query) => $query->whereHas('key.tags', fn($q) => $q->where('name', $filters['tag'])))
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    public function getTranslationById(string $id): ?Translation
    {
        return Translation::with(['key', 'group', 'language'])->find($id);
    }

    public function createTranslation(array $data): Translation
    {
        return DB::transaction(function () use ($data) {
            $key = TranslationKey::firstOrCreate(['key' => $data['key']], [
                'description' => $data['key_description'] ?? null
            ]);

            $group = TranslationGroup::firstOrCreate(['name' => $data['group']], [
                'description' => $data['group_description'] ?? null
            ]);

            if (isset($data['tags'])) {
                $tags = collect($data['tags'])->map(function ($tagName) {
                    return TranslationTag::firstOrCreate(['name' => $tagName])->id;
                });
                $key->tags()->sync($tags);
            }

            return Translation::create([
                'translation_key_id' => $key->id,
                'translation_group_id' => $group->id,
                'language_code' => $data['language_code'],
                'value' => $data['value']
            ]);
        });
    }

    public function updateTranslation(string $id, array $data): ?Translation
    {
        return DB::transaction(function () use ($id, $data) {
            $translation = Translation::find($id);
            if (!$translation) {
                return null;
            }

            if (isset($data['key'])) {
                $key = TranslationKey::firstOrCreate(['key' => $data['key']], [
                    'description' => $data['key_description'] ?? null
                ]);
                $translation->translation_key_id = $key->id;
            }

            if (isset($data['group'])) {
                $group = TranslationGroup::firstOrCreate(['name' => $data['group']], [
                    'description' => $data['group_description'] ?? null
                ]);
                $translation->translation_group_id = $group->id;
            }

            if (isset($data['language_code'])) {
                $translation->language_code = $data['language_code'];
            }

            if (isset($data['value'])) {
                $translation->value = $data['value'];
            }

            if (isset($data['tags'])) {
                $tags = collect($data['tags'])->map(function ($tagName) {
                    return TranslationTag::firstOrCreate(['name' => $tagName])->id;
                });
                $translation->key->tags()->sync($tags);
            }

            $translation->save();
            return $translation->fresh(['key', 'group', 'language']);
        });
    }

    public function deleteTranslation(string $id): bool
    {
        $translation = Translation::find($id);
        if ($translation) {
            return $translation->delete();
        }
        return false;
    }

    public function searchTranslations(string $query, array $filters = [], int $perPage = 10): Paginator
    {
        return Translation::with(['key', 'group', 'language'])
            ->where(function ($q) use ($query) {
                $q->where('value', 'like', "%{$query}%")
                    ->orWhereHas('key', fn($q) => $q->where('key', 'like', "%{$query}%"))
                    ->orWhereHas('group', fn($q) => $q->where('name', 'like', "%{$query}%"));
            })
            ->when(isset($filters['language']), fn($query) => $query->where('language_code', $filters['language']))
            ->when(isset($filters['tag']), fn($query) => $query->whereHas('key.tags', fn($q) => $q->where('name', $filters['tag'])))
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    public function getTranslationsForExport(string $locale, ?string $group = null): array
    {
        // return Translation::with(['key', 'group'])
        //     ->where('language_code', $locale)
        //     ->when($group, fn($query) => $query->whereHas('group', fn($q) => $q->where('name', $group)))
        //     ->get()
        //     ->groupBy('group.name')
        //     ->map(function ($translations) {
        //         return $translations->mapWithKeys(function ($translation) {
        //             return [$translation->key->key => $translation->value];
        //         });
        //     })
        //     ->toArray();

        $query = Translation::with([
            'key' => fn($query) => $query->select('id', 'key'),
            'group' => fn($query) => $query->select('id', 'name'),
        ])
        ->select('translation_key_id', 'translation_group_id', 'value')
        ->where('language_code', $locale);
    
        if ($group) {
            $query->whereHas('group', fn($q) => $q->where('name', $group));
        }
    
        return $query->get()
            ->groupBy('group.name')
            ->map(function ($translations) {
                return $translations->mapWithKeys(function ($translation) {
                    return [$translation->key->key => $translation->value];
                });
            })
            ->toArray();
    }

}