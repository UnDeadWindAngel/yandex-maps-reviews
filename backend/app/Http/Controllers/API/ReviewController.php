<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\YandexMapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    protected $yandexService;

    public function __construct(YandexMapsService $yandexService)
    {
        $this->yandexService = $yandexService;
    }

    // Получить отзывы с пагинацией (по 5)
    public function index(Request $request)
    {
        $organization = $request->user()->organization;

        if (!$organization) {
            return response()->json(['message' => 'Организация не настроена'], 404);
        }

        $page = $request->get('page', 1);
        $perPage = 5;

        // Сервис должен возвращать массив: [reviews, rating, totalCount]
        $data = $this->yandexService->getReviews($organization->org_id, $page, $perPage);

        return response()->json($data);
    }

    // Получить рейтинг и общее количество
    public function summary(Request $request)
    {
        $organization = $request->user()->organization;

        if (!$organization) {
            return response()->json(['message' => 'Организация не настроена'], 404);
        }

        $rating = $this->yandexService->getRating($organization->org_id);
        $count = $this->yandexService->getCount($organization->org_id);

        return response()->json([
            'rating' => $rating,
            'total_count' => $count,
        ]);
    }
}
