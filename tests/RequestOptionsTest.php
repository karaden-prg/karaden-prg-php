<?php

namespace Karaden;

use Karaden\Exception\InvalidRequestOptionsException;
use PHPUnit\Framework\TestCase;

class RequestOptionsTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /**
     * @test
     */
    public function getBaseUriはapiBaseとtenantIdを半角スラッシュで結合した値()
    {
        $apiBase = TestHelper::API_BASE;
        $tenantId = TestHelper::TENANT_ID;

        $requestOptions = RequestOptions::newBuilder()
            ->withApiBase($apiBase)
            ->withTenantId($tenantId)
            ->build();

        $this->assertEquals("$apiBase/$tenantId", $requestOptions->getBaseUri());
    }

    /**
     * @test
     */
    public function マージ元がnullならばマージ先を上書きしない()
    {
        $apiKey = TestHelper::API_KEY;
        $requestOptions = [
            RequestOptions::newBuilder()->withApiKey($apiKey)->build(),
            RequestOptions::newBuilder()->build(),
        ];

        $merged = $requestOptions[0]->merge($requestOptions[1]);

        $this->assertEquals($apiKey, $merged->apiKey);
    }

    /**
     * @test
     */
    public function マージ元がnullでなければマージ先を上書きする()
    {
        $apiKeys = ['a', 'b'];
        $requestOptions = [
            RequestOptions::newBuilder()->withApiKey($apiKeys[0])->build(),
            RequestOptions::newBuilder()->withApiKey($apiKeys[1])->build(),
        ];

        $merged = $requestOptions[0]->merge($requestOptions[1]);

        $this->assertEquals($apiKeys[1], $merged->apiKey);
    }

    public function apiVersionProvider(): array
    {
        return [
            [null],
            [''],
        ];
    }

    /**
     * @dataProvider apiVersionProvider
     * @test
     */
    public function apiVersionがnullや空文字はエラー($apiVersion)
    {
        $this->expectException(InvalidRequestOptionsException::class);
        RequestOptions::newBuilder()
            ->withApiVersion($apiVersion)
            ->build()
            ->validate();
    }

    public function apiKeyProvider(): array
    {
        return [
            [null],
            [''],
        ];
    }

    /**
     * @dataProvider apiKeyProvider
     * @test
     */
    public function apiKeyがnullや空文字はエラー($apiKey)
    {
        $this->expectException(InvalidRequestOptionsException::class);
        RequestOptions::newBuilder()
            ->withApiKey($apiKey)
            ->build()
            ->validate();
    }

    public function apiBaseProvider(): array
    {
        return [
            [null],
            [''],
        ];
    }

    /**
     * @dataProvider apiBaseProvider
     * @test
     */
    public function apiBaseがnullや空文字はエラー($apiBase)
    {
        $this->expectException(InvalidRequestOptionsException::class);
        RequestOptions::newBuilder()
            ->withApiBase($apiBase)
            ->build()
            ->validate();
    }

    public function tenantIdProvider(): array
    {
        return [
            [null],
            [''],
        ];
    }

    /**
     * @dataProvider tenantIdProvider
     * @test
     */
    public function tenantIdがnullや空文字はエラー($tenantId)
    {
        $this->expectException(InvalidRequestOptionsException::class);
        RequestOptions::newBuilder()
            ->withTenantId($tenantId)
            ->build()
            ->validate();
    }
}
