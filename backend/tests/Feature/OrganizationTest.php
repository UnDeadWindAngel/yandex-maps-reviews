<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use RefreshDatabase; // это будет использовать MySQL, т.к. мы настроили .env.testing

    public function test_authenticated_user_can_store_organization_url()
    {
        // Создаём пользователя
        $user = User::factory()->create();

        // Аутентифицируем его (Sanctum SPA использует сессии, но для API-тестов можно использовать actingAs)
        $response = $this->actingAs($user)->postJson('/api/organization', [
            'yandex_url' => 'https://yandex.ru/maps/org/test_org_123/'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'yandex_url' => 'https://yandex.ru/maps/org/test_org_123/',
                'org_id' => 'test_org_123',
            ]);

        // Проверим, что в БД сохранилось
        $this->assertDatabaseHas('organizations', [
            'user_id' => $user->id,
            'org_id' => 'test_org_123',
        ]);
    }

    public function test_unauthenticated_user_cannot_store_organization()
    {
        $response = $this->postJson('/api/organization', [
            'yandex_url' => 'https://yandex.ru/maps/org/test_org_123/'
        ]);

        $response->assertStatus(401); // Неавторизован
    }
}
