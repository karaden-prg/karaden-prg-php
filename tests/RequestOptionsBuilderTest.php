<?php

namespace Karaden;

use PHPUnit\Framework\TestCase;

class RequestOptionsBuilderTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /**
     * @test
     */
    public function apiVersionを入力できる()
    {
        $expected = '2023-01-01';
        $requestOptions = (new RequestOptionsBuilder())
            ->withApiVersion($expected)
            ->build();

        $this->assertEquals($expected, $requestOptions->apiVersion);
    }

    /**
     * @test
     */
    public function apiBaseを入力できる()
    {
        $expected = TestHelper::API_BASE;
        $requestOptions = (new RequestOptionsBuilder())
            ->withApiBase($expected)
            ->build();

        $this->assertEquals($expected, $requestOptions->apiBase);
    }

    /**
     * @test
     */
    public function apiKeyを入力できる()
    {
        $expected = TestHelper::API_KEY;
        $requestOptions = (new RequestOptionsBuilder())
            ->withApiKey($expected)
            ->build();

        $this->assertEquals($expected, $requestOptions->apiKey);
    }

    /**
     * @test
     */
    public function tenantIdを入力できる()
    {
        $expected = TestHelper::TENANT_ID;
        $requestOptions = (new RequestOptionsBuilder())
            ->withTenantId($expected)
            ->build();

        $this->assertEquals($expected, $requestOptions->tenantId);
    }

    /**
     * @test
     */
    public function userAgentを入力できる()
    {
        $expected = 'userAgent';
        $requestOptions = (new RequestOptionsBuilder())
            ->withUserAgent($expected)
            ->build();

        $this->assertEquals($expected, $requestOptions->userAgent);
    }
}
 