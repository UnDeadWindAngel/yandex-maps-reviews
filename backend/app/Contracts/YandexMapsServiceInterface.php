<?php

namespace App\Contracts;

interface YandexMapsServiceInterface
{
    /**
     * Извлечь ID организации из URL карточки Яндекс.Карт
     */
    public function parseOrgIdFromUrl(string $url): ?string;

    /**
     * Получить отзывы с пагинацией
     * @return array{
     *     reviews: array,
     *     rating: float,
     *     total_count: int,
     *     current_page: int,
     *     per_page: int,
     *     last_page: int
     * }
     */
    public function getReviews(string $orgId, int $page = 1, int $perPage = 5): array;

    /**
     * Получить средний рейтинг организации
     */
    public function getRating(string $orgId): float;

    /**
     * Получить общее количество отзывов
     */
    public function getCount(string $orgId): int;
}
