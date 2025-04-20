<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class TranslationCacheService
{
    public function getTranslations(string $locale, ?string $group = null): array
    {
        $cacheKey = $this->getCacheKey($locale, $group);

        return Cache::remember($cacheKey, config('translations.cache_ttl'), function () use ($locale, $group) {
            return app(TranslationService::class)->exportTranslations($locale, $group);
        });
    }

    public function clearCache(string $locale, ?string $group = null): void
    {
        $cacheKey = $this->getCacheKey($locale, $group);
        Cache::forget($cacheKey);
    }

    protected function getCacheKey(string $locale, ?string $group = null): string
    {
        return $group 
            ? "translations.{$locale}.{$group}"
            : "translations.{$locale}";
    }
}