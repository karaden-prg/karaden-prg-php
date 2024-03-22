<?php

namespace Karaden\Model;

use DateTimeInterface;
use GuzzleHttp\Psr7\Response;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Discovery\Strategy\MockClientStrategy;
use Http\Message\RequestMatcher;
use Http\Mock\Client;
use Karaden\Param\Message\Bulk\BulkMessageCreateParams;
use Karaden\Param\Message\Bulk\BulkMessageListMessageParams;
use Karaden\Param\Message\Bulk\BulkMessageShowParams;
use Karaden\TestHelper;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class BulkMessageTest extends TestCase
{
    protected Client $httpClient;
    protected RequestMatcher $matcher;

    protected function setUp(): void
    {
        Psr18ClientDiscovery::prependStrategy(MockClientStrategy::class);

        $this->httpClient = TestHelper::getRequestableHttpClient(BulkMessage::class);
        $this->matcher = new class implements RequestMatcher
        {
            public function matches(RequestInterface $request): bool
            {
                return true;
            }
        };
    }

    protected function tearDown(): void
    {
        TestHelper::removeRequestableHttpClient(BulkMessage::class);
    }

    /**
     * @test
     */
    public function 一括送信メッセージを作成できる()
    {
        $object = ['object' => 'bulk_message'];
        $params = BulkMessageCreateParams::newBuilder()
            ->withBulkFileId('c439f89c-1ea3-7073-7021-1f127a850437')
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()->build();

        $callback = function (RequestInterface $request) use ($object, $requestOptions) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals(sprintf('%s/messages/bulks', $requestOptions->getBaseUri()), $request->getUri());
            $this->assertEquals('bulk_file_id=c439f89c-1ea3-7073-7021-1f127a850437', $request->getBody()->getContents());
            $this->assertEquals('application/x-www-form-urlencoded', $request->getHeader('Content-Type')[0]);

            return new Response(200, [], json_encode($object));
        };
        $this->httpClient->on($this->matcher, $callback);

        $bulkMessage = BulkMessage::create($params, $requestOptions);

        $this->assertEquals($object['object'], $bulkMessage->getObject());
    }

    /**
     * @test
     */
    public function 一括送信メッセージの詳細を取得できる()
    {
        $object = ['object' => 'bulk_message'];
        $params = BulkMessageShowParams::newBuilder()
            ->withId('c439f89c-1ea3-7073-7021-1f127a850437')
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()->build();

        $callback = function (RequestInterface $request) use ($object, $params, $requestOptions) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals(sprintf('%s/messages/bulks/%s', $requestOptions->getBaseUri(), $params->id), $request->getUri());

            return new Response(200, [], json_encode($object));
        };
        $this->httpClient->on($this->matcher, $callback);

        $bulkMessage = BulkMessage::show($params, $requestOptions);

        $this->assertEquals($object['object'], $bulkMessage->getObject());
    }

    /**
     * @test
     */
    public function 一括送信メッセージの結果を取得できる()
    {
        $object = ['object' => 'bulk_message'];
        $params = BulkMessageListMessageParams::newBuilder()
            ->withId('c439f89c-1ea3-7073-7021-1f127a850437')
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()->build();
        $expectUrl = 'http://example.com';

        $callback = function (RequestInterface $request) use ($object, $params, $requestOptions, $expectUrl) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals(sprintf('%s/messages/bulks/%s/messages', $requestOptions->getBaseUri(), $params->id), $request->getUri());

            return new Response(302, ['Location' => $expectUrl], json_encode($object));
        };
        $this->httpClient->on($this->matcher, $callback);

        $output = BulkMessage::listMessage($params, $requestOptions);

        $this->assertEquals($output, $expectUrl);
    }

    public function locationProvider()
    {
        return [
            ['location'],
            ['LOCATION'],
        ];
    }

    /**
     * @test
     * @dataProvider locationProvider
     */
    public function Locationが大文字小文字関係なく一括送信メッセージの結果を取得できる($location)
    {
        $object = ['object' => 'bulk_message'];
        $params = BulkMessageListMessageParams::newBuilder()
            ->withId('c439f89c-1ea3-7073-7021-1f127a850437')
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()->build();
        $expectUrl = 'http://example.com';

        $callback = function (RequestInterface $request) use ($object, $params, $requestOptions, $expectUrl, $location) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals(sprintf('%s/messages/bulks/%s/messages', $requestOptions->getBaseUri(), $params->id), $request->getUri());

            return new Response(302, [$location => $expectUrl], json_encode($object));
        };
        $this->httpClient->on($this->matcher, $callback);

        $output = BulkMessage::listMessage($params, $requestOptions);

        $this->assertEquals($output, $expectUrl);
    }

    /**
     * @test
     */
    public function statusを出力できる()
    {
        $value = 'processing';
        $bulkMessage = new BulkMessage();
        $bulkMessage->setProperty('status', $value);

        $this->assertEquals($value, $bulkMessage->getStatus());
    }

    /**
     * @test
     */
    public function 受付エラーがない場合はerrorは出力されない()
    {
        $error = null;
        $bulkMessage = new BulkMessage();
        $bulkMessage->setProperty('error', $error);

        $this->assertEquals($error, $bulkMessage->getError());
    }

    /**
     * @test
     */
    public function 受付エラーがあった場合はerrorが出力される()
    {
        $error = new Error();
        $bulkMessage = new BulkMessage();
        $bulkMessage->setProperty('error', $error);

        $this->assertInstanceOf(Error::class, $bulkMessage->getError());
    }

    public function dateProvider(): array
    {
        return [
            ['2022-12-09T00:00:00+09:00'],
            [null],
        ];
    }

    /**
     * @test
     * @dataProvider dateProvider
     */
    public function createdAtを出力できる($value)
    {
        $bulkMessage = new BulkMessage();
        $bulkMessage->setProperty('created_at', $value);

        $this->assertEquals($value, $bulkMessage->getCreatedAt() ? $bulkMessage->getCreatedAt()->format(DateTimeInterface::ATOM) : null);
    }

    /**
     * @test
     * @dataProvider dateProvider
     */
    public function updatedAtを出力できる($value)
    {
        $bulkMessage = new BulkMessage();
        $bulkMessage->setProperty('updated_at', $value);

        $this->assertEquals($value, $bulkMessage->getUpdatedAt() ? $bulkMessage->getUpdatedAt()->format(DateTimeInterface::ATOM) : null);
    }
}
