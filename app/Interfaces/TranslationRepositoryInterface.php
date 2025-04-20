<?php

namespace App\Interfaces;

interface TranslationRepositoryInterface
{
    public function getAllTranslations(array $filters = [], int $perPage = 10);
    public function getTranslationById(string $id);
    public function createTranslation(array $data);
    public function updateTranslation(string $id, array $data);
    public function deleteTranslation(string $id);
    public function searchTranslations(string $query, array $filters = [], int $perPage = 10);
    public function getTranslationsForExport(string $locale, ?string $group = null);
}