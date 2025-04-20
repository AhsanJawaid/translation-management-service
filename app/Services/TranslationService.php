<?php

namespace App\Services;

use App\Interfaces\TranslationRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;

class TranslationService
{
    public function __construct(
        private TranslationRepositoryInterface $translationRepository
    ) {}

    public function getAllTranslations(array $filters = [], int $perPage = 10): Paginator
    {
        return $this->translationRepository->getAllTranslations($filters, $perPage);
    }

    public function getTranslation(string $id): ?array
    {
        $translation = $this->translationRepository->getTranslationById($id);
        return $translation ? $this->formatTranslation($translation) : null;
    }

    public function createTranslation(array $data): array
    {
        $translation = $this->translationRepository->createTranslation($data);
        return $this->formatTranslation($translation);
    }

    public function updateTranslation(string $id, array $data): ?array
    {
        $translation = $this->translationRepository->updateTranslation($id, $data);
        return $translation ? $this->formatTranslation($translation) : null;
    }

    public function deleteTranslation(string $id): bool
    {
        return $this->translationRepository->deleteTranslation($id);
    }

    public function searchTranslations(string $query, array $filters = [], int $perPage = 10): Paginator
    {
        return $this->translationRepository->searchTranslations($query, $filters, $perPage);
    }

    public function exportTranslations(string $locale, ?string $group = null): array
    {
        return $this->translationRepository->getTranslationsForExport($locale, $group);
    }

    private function formatTranslation(Translation $translation): array
    {
        return [
            'id' => $translation->id,
            'key' => $translation->key->key,
            'key_description' => $translation->key->description,
            'group' => $translation->group->name,
            'group_description' => $translation->group->description,
            'language_code' => $translation->language_code,
            'value' => $translation->value,
            'tags' => $translation->key->tags->pluck('name'),
            'created_at' => $translation->created_at,
            'updated_at' => $translation->updated_at,
        ];
    }
}