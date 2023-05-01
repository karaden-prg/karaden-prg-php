<?php

namespace Karaden\Net;

use Karaden\Config;
use Karaden\RequestOptions;
use Karaden\TestHelper;
use GuzzleHttp\Psr7\Response;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Strategy\MockClientStrategy;
use Http\Message\RequestMatcher;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class RequestorTest extends TestCase
{
    protected Requestor $request;
    protected Client $httpClient;
    protected RequestMatcher $matcher;

    public function methodProvider(): array
    {
        return [
            ['post'],
            ['get'],
            ['put'],
            ['delete'],
            ['option'],
            ['head'],
        ];
    }

    protected function setUp(): void
    {
        HttpClientDiscovery::prependStrategy(MockClientStrategy::class);
        $this->request = new Requestor();
        $this->httpClient = TestHelper::getHttpClient($this->request);
        $this->matcher = new class implements RequestMatcher
        {
            public function matches(RequestInterface $request): bool
            {
                return true;
            }
        };
    }

    /**
     * @test
     */
    public function ベースURLとパスが結合される()
    {
        $path = '/test';
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $callback = function(RequestInterface $request) use($path, $requestOptions) {
            $this->assertEquals(Config::asRequestOptions()->merge($requestOptions)->getBaseUri() . $path, $request->getUri());

            return new Response(200, ['Content-Type' => 'application/json'], '');
        };
        $this->httpClient->on($this->matcher, $callback);

        ($this->request)('GET', $path, null, null, null, $requestOptions);
    }

    /**
     * @test
     * @dataProvider methodProvider
     */
    public function メソッドがHTTPクライアントに伝わる(string $method)
    {
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $callback = function(RequestInterface $request) use($method) {
            $this->assertEqualsIgnoringCase($method, $request->getMethod());

            return new Response(200, [], '');
        };
        $this->httpClient->on($this->matcher, $callback);

        ($this->request)($method, '/test', null, null, null, $requestOptions);
    }

    /**
     * @test
     */
    public function URLパラメータがHTTPクライアントに伝わる()
    {
        $path = '/test';
        $params = ['key1' => 'value1', 'key2' => 'value2', ];
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $callback = function(RequestInterface $request) use($path, $params, $requestOptions) {
            $this->assertEquals(Config::asRequestOptions()->merge($requestOptions)->getBaseUri() . $path . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986), $request->getUri());

            return new Response(200, [], '');
        };
        $this->httpClient->on($this->matcher, $callback);

        ($this->request)('GET', $path, null, $params, null, $requestOptions);
    }

    /**
     * @test
     */
    public function 本文がHTTPクライアントに伝わる()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2', ];
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $callback = function(RequestInterface $request) use($data) {
            $expected = http_build_query($data);
            $actual = $request->getBody()->getContents();
            $this->assertEqualsIgnoringCase($expected, $actual);

            return new Response(200, [], '');
        };
        $this->httpClient->on($this->matcher, $callback);

        ($this->request)('POST', '/test', null, null, $data, $requestOptions);
    }

    /**
     * @test
     */
    public function リクエスト時に指定したリクエストオプションはコンストラクタのリクエストオプションを上書きする()
    {
        $apiKey = '456';
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->withApiKey($apiKey)
            ->build();

        $callback = function(RequestInterface $request) use($apiKey) {
            $header = $request->getHeader('Authorization');
            $this->assertCount(1, $header);
            $this->assertEquals("Bearer $apiKey", $header[0]);

            return new Response(200, [], '');
        };
        $this->httpClient->on($this->matcher, $callback);

        ($this->request)('GET', '/test', null, null, null, $requestOptions);
    }

    /**
     * @test
     */
    public function APIキーに基づいてBearer認証ヘッダを出力する()
    {
        $apiKey = '456';
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->withApiKey($apiKey)
            ->build();

        $callback = function(RequestInterface $request) use($apiKey) {
            $header = $request->getHeader('Authorization');
            $this->assertCount(1, $header);
            $this->assertEquals("Bearer $apiKey", $header[0]);

            return new Response(200, [], '');
        };
        $this->httpClient->on($this->matcher, $callback);

        ($this->request)('GET', '/test', null, null, null, $requestOptions);
    }

    /**
     * @test
     */
    public function APIバージョンを設定した場合はAPIバージョンヘッダを出力する()
    {
        $apiVersion = '2023-01-01';
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->withApiVersion($apiVersion)
            ->build();

        $callback = function(RequestInterface $request) use($apiVersion) {
            $header = $request->getHeader('Karaden-Version');
            $this->assertCount(1, $header);
            $this->assertEquals($apiVersion, $header[0]);

            return new Response(200, [], '');
        };
        $this->httpClient->on($this->matcher, $callback);

        ($this->request)('GET', '/test', null, null, null, $requestOptions);
    }

    /**
     * @test
     */
    public function APIバージョンを設定しない場合はデフォルトのAPIバージョンヘッダを出力する()
    {
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $callback = function(RequestInterface $request) {
            $header = $request->getHeader('Karaden-Version');
            $this->assertEquals(Config::DEFALUT_API_VERSION, $header[0]);

            return new Response(200, [], '');
        };
        $this->httpClient->on($this->matcher, $callback);

        ($this->request)('GET', '/test', null, null, null, $requestOptions);
    }
}
