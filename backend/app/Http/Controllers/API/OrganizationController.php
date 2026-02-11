<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\YandexMapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrganizationController extends Controller
{
    protected $yandexService;

    public function __construct(YandexMapsService $yandexService)
    {
        $this->yandexService = $yandexService;
    }

    // Получить настройки организации текущего пользователя
    public function show(Request $request)
    {
        $organization = $request->user()->organization;
        return response()->json($organization);
    }

    // Сохранить или обновить ссылку на Яндекс Карты
    public function store(Request $request)
    {
        $request->validate([
            'yandex_url' => 'required|url|regex:/^https:\/\/yandex\.(ru|by|kz|ua|com)\/maps\/org\/.*/i',
        ]);

        $url = $request->yandex_url;
        $orgId = $this->yandexService->parseOrgIdFromUrl($url);

        if (!$orgId) {
            throw ValidationException::withMessages([
                'yandex_url' => ['Не удалось извлечь идентификатор организации из ссылки.'],
            ]);
        }

        $organization = $request->user()->organization()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'yandex_url' => $url,
                'org_id' => $orgId,
                'name' => null, // Можно запросить из API позже
            ]
        );

        return response()->json($organization, 201);
    }
}
