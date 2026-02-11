<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class YandexMapsService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.yandex.api_key');
        $this->client = new Client([
            'base_uri' => 'https://business.yandex.ru/api/',
            'timeout'  => 10.0,
        ]);
    }

    /**
     * Извлечь org_id из URL карточки организации
     * Пример URL: https://yandex.ru/maps/org/abc123/ или https://yandex.ru/maps/org/abc123?ll=...
     */
    public function parseOrgIdFromUrl(string $url): ?string
    {
        preg_match('/\/org\/([^\/?#]+)/', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Получить отзывы с пагинацией
     */
    public function getReviews(string $orgId, int $page = 1, int $perPage = 5): array
    {
        $cacheKey = "reviews_{$orgId}_page_{$page}";

        // Проверим актуальность по количеству отзывов
        $currentCount = $this->getCount($orgId);
        $cachedCount = Cache::get("count_{$orgId}");

        if ($cachedCount !== $currentCount) {
            // Количество изменилось — сбрасываем кеш отзывов
            $this->clearReviewsCache($orgId);
            Cache::put("count_{$orgId}", $currentCount, now()->addMinutes(5));
        }

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($orgId, $page, $perPage) {
            // TODO: Здесь должен быть реальный запрос к API Яндекс Бизнеса.
            // Пока возвращаем мок-данные для тестирования.
            // В продакшене замените на настоящий эндпоинт и обработку.

            // Имитация задержки
            sleep(1);

            $reviews = [];
            for ($i = 0; $i < $perPage; $i++) {
                $reviews[] = [
                    'id' => $i + ($page - 1) * $perPage + 1,
                    'author_name' => 'Пользователь ' . rand(100, 999),
                    'text' => 'Отличное место! Всё понравилось. Приду ещё.',
                    'rating' => rand(4, 5),
                    'date' => now()->subDays(rand(1, 30))->toDateString(),
                    'photo' => rand(0, 1) ? 'https://via.placeholder.com/150' : null,
                ];
            }

            $rating = $this->getRating($orgId);
            $totalCount = $this->getCount($orgId);

            return [
                'reviews' => $reviews,
                'rating' => $rating,
                'total_count' => $totalCount,
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => ceil($totalCount / $perPage),
            ];
        });
    }

    /**
     * Получить средний рейтинг
     */
    public function getRating(string $orgId): float
    {
        return Cache::remember("rating_{$orgId}", now()->addMinutes(5), function () use ($orgId) {
            // Заглушка
            return 4.7;
        });
    }

    /**
     * Получить общее количество отзывов
     */
    public function getCount(string $orgId): int
    {
        return Cache::remember("count_{$orgId}", now()->addMinutes(5), function () use ($orgId) {
            // Заглушка
            return 124;
        });
    }

    /**
     * Очистить кеш отзывов для организации
     */
    protected function clearReviewsCache(string $orgId): void
    {
        // Удаляем все страницы отзывов
        for ($i = 1; $i <= 100; $i++) {
            Cache::forget("reviews_{$orgId}_page_{$i}");
        }
    }
}
