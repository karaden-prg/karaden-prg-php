<?php

namespace Karaden;

use PHPUnit\Framework\TestCase;
use Http\Client\HttpClient;
use Http\Message\Formatter;
use Psr\Log\LoggerInterface;

class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
        Config::reset();
    }

    /**
     * @test
     */
    public function 入力したapiBaseが取得したRequestOptionsに入力されること()
    {
        $expected = TestHelper::API_BASE;
        Config::$apiBase = $expected;
        $requestOptions = Config::asRequestOptions();

        $this->assertNotNull($expected, $requestOptions->apiBase);
    }

    /**
     * @test
     */
    public function 入力したapiKeyが取得したRequestOptionsに入力されること()
    {
        $expected = TestHelper::API_KEY;
        Config::$apiKey = $expected;
        $requestOptions = Config::asRequestOptions();

        $this->assertNotNull($expected, $requestOptions->apiKey);
    }

    /**
     * @test
     */
    public function 入力したtenantIdが取得したRequestOptionsに入力されること()
    {
        $expected = TestHelper::TENANT_ID;
        Config::$tenantId = $expected;
        $requestOptions = Config::asRequestOptions();

        $this->assertNotNull($expected, $requestOptions->tenantId);
    }

    /**
     * @test
     */
    public function 入力したapiVersionが取得したRequestOptionsに入力されること()
    {
        $expected = '2023-01-01';
        Config::$apiVersion = $expected;
        $requestOptions = Config::asRequestOptions();

        $this->assertNotNull($expected, $requestOptions->apiVersion);
    }

    /**
     * @test
     */
    public function 入力したuserAgentが取得したRequestOptionsに入力されること()
    {
        $expected = 'userAgent';
        Config::$userAgent = $expected;
        $requestOptions = Config::asRequestOptions();

        $this->assertNotNull($expected, $requestOptions->userAgent);
    }

    public function testReset()
    {
        Config::$httpClient = $this->getMockBuilder(HttpClient::class)->getMock();
        Config::$logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        Config::$formatter = $this->getMockBuilder(Formatter::class)->getMock();
        Config::$apiVersion = 'apiVersion';
        Config::$apiKey = 'apiKey';
        Config::$tenantId = 'tenantId';
        Config::$userAgent = 'userAgent';
        Config::$apiBase = 'apiBase';

        $this->assertInstanceOf(HttpClient::class, Config::$httpClient);
        $this->assertInstanceOf(LoggerInterface::class, Config::$logger);
        $this->assertInstanceOf(Formatter::class, Config::$formatter);
        $this->assertNotNull(Config::$apiVersion);
        $this->assertNotNull(Config::$apiKey);
        $this->assertNotNull(Config::$tenantId);
        $this->assertNotNull(Config::$userAgent);
        $this->assertNotNull(Config::$apiBase);

        Config::reset();

        $this->assertNull(Config::$httpClient);
        $this->assertNull(Config::$logger);
        $this->assertNull(Config::$formatter);
        $this->assertSame(Config::DEFALUT_API_VERSION, Config::$apiVersion);
        $this->assertNull(Config::$apiKey);
        $this->assertNull(Config::$tenantId);
        $this->assertNull(Config::$userAgent);
        $this->assertSame(Config::DEFAULT_API_BASE, Config::$apiBase);
    }
}
 