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

        // Проверяем актуальность по количеству отзывов
        $currentCount = $this->getCount($orgId);
        $cachedCount = Cache::get("count_{$orgId}");

        if ($cachedCount !== $currentCount) {
            $this->clearReviewsCache($orgId);
            Cache::put("count_{$orgId}", $currentCount, now()->addMinutes(5));
        }

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($orgId, $page, $perPage) {
            // Генерируем псевдослучайные, но стабильные данные на основе org_id
            $seed = crc32($orgId . '_' . $page);
            mt_srand($seed);

            $totalCount = $this->getCount($orgId);
            $reviews = [];

            // Сколько отзывов на этой странице (на последней может быть меньше)
            $offset = ($page - 1) * $perPage;
            $remaining = max(0, $totalCount - $offset);
            $itemsOnPage = min($perPage, $remaining);

            for ($i = 0; $i < $itemsOnPage; $i++) {
                $reviewId = $offset + $i + 1;

                // Детерминированная генерация на основе org_id, страницы и индекса
                $reviewSeed = crc32($orgId . '_' . $page . '_' . $i);
                mt_srand($reviewSeed);

                $names = ['Алексей', 'Мария', 'Дмитрий', 'Елена', 'Сергей', 'Анна', 'Иван', 'Ольга', 'Павел', 'Татьяна'];
                $texts = [
                    'Отличное место, всё понравилось!',
                    'Хороший сервис, буду рекомендовать.',
                    'Неплохо, но есть к чему стремиться.',
                    'В целом доволен, спасибо!',
                    'Очень вежливый персонал.',
                    'Уютно и чисто.',
                    'Цены приемлемые, качество на уровне.',
                    'Есть небольшие недочёты, но в целом хорошо.',
                    'Приду ещё не раз!',
                    'Соотношение цены и качества отличное.'
                ];

                $reviews[] = [
                    'id' => $reviewId,
                    'author_name' => $names[mt_rand(0, count($names) - 1)] . ' ' . mt_rand(100, 999),
                    'text' => $texts[mt_rand(0, count($texts) - 1)],
                    'rating' => mt_rand(4, 5), // почти все отзывы хорошие
                    'date' => now()->subDays(mt_rand(1, 60))->toDateString(),
                    'photo' => mt_rand(0, 2) === 0 ? 'https://via.placeholder.com/150?text=Фото' : null,
                ];
            }

            // Восстанавливаем глобальный rand
            mt_srand();

            $rating = $this->getRating($orgId);

            return [
                'reviews' => $reviews,
                'rating' => $rating,
                'total_count' => $totalCount,
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => (int) ceil($totalCount / $perPage),
            ];
        });
    }

    /**
     * Получить средний рейтинг (уникальный для организации)
     */
    public function getRating(string $orgId): float
    {
        return Cache::remember("rating_{$orgId}", now()->addMinutes(5), function () use ($orgId) {
            $seed = crc32($orgId . '_rating');
            mt_srand($seed);
            // Рейтинг от 4.0 до 5.0 с шагом 0.1
            $rating = mt_rand(40, 50) / 10;
            mt_srand();
            return (float) $rating;
        });
    }

    /**
     * Получить общее количество отзывов (уникальное для организации)
     */
    public function getCount(string $orgId): int
    {
        return Cache::remember("count_{$orgId}", now()->addMinutes(5), function () use ($orgId) {
            $seed = crc32($orgId . '_count');
            mt_srand($seed);
            // Количество отзывов от 50 до 300
            $count = mt_rand(50, 300);
            mt_srand();
            return $count;
        });
    }

    /**
     * Очистить кеш отзывов для организации
     */
    protected function clearReviewsCache(string $orgId): void
    {
        // Удаляем все возможные страницы (достаточно первых 100)
        for ($i = 1; $i <= 100; $i++) {
            Cache::forget("reviews_{$orgId}_page_{$i}");
        }
    }
}
