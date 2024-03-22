<?php

namespace Karaden\Model;

use DateTimeInterface;
use GuzzleHttp\Psr7\Response;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Discovery\Strategy\MockClientStrategy;
use Http\Message\RequestMatcher;
use Http\Mock\Client;
use Karaden\TestHelper;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class BulkFileTest extends TestCase
{
    protected Client $httpClient;
    protected RequestMatcher $matcher;

    protected function setUp(): void
    {
        Psr18ClientDiscovery::prependStrategy(MockClientStrategy::class);

        $this->httpClient = TestHelper::getRequestableHttpClient(BulkFile::class);
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
        TestHelper::removeRequestableHttpClient(BulkFile::class);
    }

    /**
     * @test
     */
    public function 一括送信用CSVのアップロード先URLを発行できる()
    {
        $object = ['object' => 'bulk_file'];
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()->build();

        $callback = function (RequestInterface $request) use ($object, $requestOptions) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals(sprintf('%s/messages/bulks/files', $requestOptions->getBaseUri()), $request->getUri());

            return new Response(200, [], json_encode($object));
        };
        $this->httpClient->on($this->matcher, $callback);

        $bulkFile = BulkFile::create($requestOptions);

        $this->assertEquals($object['object'], $bulkFile->getObject());
    }

    /**
     * @test
     */
    public function urlを出力できる()
    {
        $value = 'https://example.com/';
        $bulkFile = new BulkFile();
        $bulkFile->setProperty('url', $value);

        $this->assertEquals($value, $bulkFile->getUrl());
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
        $bulkFile = new BulkFile();
        $bulkFile->setProperty('created_at', $value);

        $this->assertEquals($value, $bulkFile->getCreatedAt() ? $bulkFile->getCreatedAt()->format(DateTimeInterface::ATOM) : null);
    }

    /**
     * @test
     * @dataProvider dateProvider
     */
    public function expiresAtを出力できる($value)
    {
        $bulkFile = new BulkFile();
        $bulkFile->setProperty('expires_at', $value);

        $this->assertEquals($value, $bulkFile->getExpiresAt() ? $bulkFile->getExpiresAt()->format(DateTimeInterface::ATOM) : null);
    }
}
