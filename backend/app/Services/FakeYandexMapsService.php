<?php

namespace App\Services;

use App\Contracts\YandexMapsServiceInterface;
use Illuminate\Support\Facades\Cache;

class FakeYandexMapsService implements YandexMapsServiceInterface
{
    protected int $cacheTtl;

    public function __construct()
    {
        $this->cacheTtl = (int) config('services.yandex.cache_ttl', 300);
    }

    public function parseOrgIdFromUrl(string $url): ?string
    {
        preg_match('/\/org\/([^\/?#]+)/', $url, $matches);
        return $matches[1] ?? null;
    }

    public function getReviews(string $orgId, int $page = 1, int $perPage = 5): array
    {
        $cacheKey = "reviews_{$orgId}_page_{$page}";

        $currentCount = $this->getCount($orgId);
        $cachedCount = Cache::get("count_{$orgId}");

        if ($cachedCount !== $currentCount) {
            $this->clearReviewsCache($orgId);
            Cache::put("count_{$orgId}", $currentCount, now()->addSeconds($this->cacheTtl));
        }

        return Cache::remember($cacheKey, now()->addSeconds($this->cacheTtl), function () use ($orgId, $page, $perPage) {
            $seed = crc32($orgId . '_' . $page);
            mt_srand($seed);

            $totalCount = $this->getCount($orgId);
            $offset = ($page - 1) * $perPage;
            $remaining = max(0, $totalCount - $offset);
            $itemsOnPage = min($perPage, $remaining);

            $reviews = [];
            for ($i = 0; $i < $itemsOnPage; $i++) {
                $reviewId = $offset + $i + 1;
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
                    'rating' => mt_rand(4, 5),
                    'date' => now()->subDays(mt_rand(1, 60))->toDateString(),
                    'photo' => mt_rand(0, 2) === 0 ? 'https://via.placeholder.com/150?text=Фото' : null,
                ];
            }
            mt_srand();

            return [
                'reviews' => $reviews,
                'rating' => $this->getRating($orgId),
                'total_count' => $totalCount,
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => (int) ceil($totalCount / $perPage),
            ];
        });
    }

    public function getRating(string $orgId): float
    {
        return Cache::remember("rating_{$orgId}", now()->addSeconds($this->cacheTtl), function () use ($orgId) {
            $seed = crc32($orgId . '_rating');
            mt_srand($seed);
            $rating = mt_rand(40, 50) / 10;
            mt_srand();
            return (float) $rating;
        });
    }

    public function getCount(string $orgId): int
    {
        return Cache::remember("count_{$orgId}", now()->addSeconds($this->cacheTtl), function () use ($orgId) {
            $seed = crc32($orgId . '_count');
            mt_srand($seed);
            $count = mt_rand(50, 300);
            mt_srand();
            return $count;
        });
    }

    protected function clearReviewsCache(string $orgId): void
    {
        for ($i = 1; $i <= 100; $i++) {
            Cache::forget("reviews_{$orgId}_page_{$i}");
        }
    }
}
