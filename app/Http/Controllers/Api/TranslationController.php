<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    public function __construct(private TranslationService $translationService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $translations = $this->translationService->getAllTranslations(
            $request->only(['key', 'group', 'language', 'tag']),
            $request->input('per_page', 10)
        );

        return response()->json($translations);
    }

    public function store(StoreTranslationRequest $request): JsonResponse
    {
        $translation = $this->translationService->createTranslation($request->validated());
        return response()->json($translation, 201);
    }

    public function show(string $id): JsonResponse
    {
        $translation = $this->translationService->getTranslation($id);
        return $translation 
            ? response()->json($translation)
            : response()->json(['message' => 'Translation not found'], 404);
    }

    public function update(UpdateTranslationRequest $request, string $id): JsonResponse
    {
        $translation = $this->translationService->updateTranslation($id, $request->validated());
        return $translation
            ? response()->json($translation)
            : response()->json(['message' => 'Translation not found'], 404);
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->translationService->deleteTranslation($id)
            ? response()->json(['message' => 'Translation deleted'])
            : response()->json(['message' => 'Translation not found'], 404);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['query' => 'required|string']);
        
        $translations = $this->translationService->searchTranslations(
            $request->input('query'),
            $request->only(['language', 'tag']),
            $request->input('per_page', 10)
        );

        return response()->json($translations);
    }

    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'locale' => 'required|string|size:2',
            'group' => 'sometimes|string'
        ]);
    
        $cacheKey = "translations.export.{$request->input('locale')}.{$request->input('group', 'all')}";
        
        $translations = Cache::remember($cacheKey, now()->addHour(), function () use ($request) {
            return $this->translationService->exportTranslations(
                $request->input('locale'),
                $request->input('group')
            );
        });
    
        $response = response()->json($translations);
    
        // CDN headers
        $response->header('Cache-Control', 'public, max-age=3600, s-maxage=3600');
        $response->header('CDN-Cache-Control', 'public, max-age=3600');
        $response->header('Vary', 'Accept-Encoding');
        
        return $response;
    }

}