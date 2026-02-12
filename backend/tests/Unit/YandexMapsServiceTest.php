<?php

namespace Tests\Unit;

use App\Services\FakeYandexMapsService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class YandexMapsServiceTest extends TestCase
{
    #[Test]
    public function it_can_parse_org_id_from_yandex_maps_url()
    {
        $service = new FakeYandexMapsService();

        $url = 'https://yandex.ru/maps/org/abc123/';
        $this->assertEquals('abc123', $service->parseOrgIdFromUrl($url));

        $url2 = 'https://yandex.ru/maps/org/xyz789?ll=37.123,55.456';
        $this->assertEquals('xyz789', $service->parseOrgIdFromUrl($url2));

        $url3 = 'https://yandex.by/maps/org/test_org_123/';
        $this->assertEquals('test_org_123', $service->parseOrgIdFromUrl($url3));

        $url4 = 'https://example.com/not_yandex';
        $this->assertNull($service->parseOrgIdFromUrl($url4));
    }
}
