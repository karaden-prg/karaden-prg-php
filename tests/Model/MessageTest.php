<?php

namespace Karaden\Model;

use Karaden\Config;
use Karaden\Param\Message\MessageCreateParams;
use Karaden\RequestOptions;
use Karaden\TestHelper;
use Karaden\Model\Message;
use Karaden\Param\Message\MessageCancelParams;
use Karaden\Param\Message\MessageDetailParams;
use Karaden\Param\Message\MessageListParams;
use DateTimeInterface;
use GuzzleHttp\Psr7\Response;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Strategy\MockClientStrategy;
use Http\Message\RequestMatcher;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class MessageTest extends TestCase
{
    protected Client $httpClient;
    protected RequestMatcher $matcher;

    protected function setUp(): void
    {
        HttpClientDiscovery::prependStrategy(MockClientStrategy::class);

        $this->httpClient = TestHelper::getRequestableHttpClient(Message::class);
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
        TestHelper::removeRequestableHttpClient(Message::class);
    }

    /**
     * @test
     */
    public function メッセージを作成できる()
    {
        $object = ['object' => 'message'];
        $params = MessageCreateParams::newBuilder()
            ->withServiceId(1)
            ->withTo('to')
            ->withBody('body')
            ->withTags(['a', 'b'])
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $callback = function(RequestInterface $request) use($object, $requestOptions) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals(sprintf('%s/messages', $requestOptions->getBaseUri()), $request->getUri());
            $this->assertEquals('service_id=1&to=to&body=body&tags%5B0%5D=a&tags%5B1%5D=b', $request->getBody()->getContents());
            $this->assertEquals('application/x-www-form-urlencoded', $request->getHeader('Content-Type')[0]);

            return new Response(200, [], json_encode($object));
        };
        $this->httpClient->on($this->matcher, $callback);

        $message = Message::create($params, $requestOptions);

        $this->assertEquals($object['object'], $message->getObject());
    }

    /**
     * @test
     */
    public function メッセージの詳細を取得できる()
    {
        $object = ['object' => 'message'];
        $params = MessageDetailParams::newBuilder()
            ->withId('id')
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $callback = function(RequestInterface $request) use($object, $params, $requestOptions) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals(sprintf('%s/messages/%s', $requestOptions->getBaseUri(), $params->id), $request->getUri());

            return new Response(200, [], json_encode($object));
        };
        $this->httpClient->on($this->matcher, $callback);

        $message = Message::detail($params, $requestOptions);

        $this->assertEquals($object['object'], $message->getObject());
    }

    /**
     * @test
     */
    public function メッセージの一覧を取得できる()
    {
        $object = ['object' => 'list'];
        $params = MessageListParams::newBuilder()
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $callback = function(RequestInterface $request) use($object, $params, $requestOptions) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals(sprintf('%s/messages', $requestOptions->getBaseUri()), $request->getUri());

            return new Response(200, [], json_encode($object));
        };
        $this->httpClient->on($this->matcher, $callback);

        $list = Message::list($params, $requestOptions);

        $this->assertEquals($object['object'], $list->getObject());
    }

    /**
     * @test
     */
    public function メッセージの送信をキャンセルできる()
    {
        $object = ['object' => 'message'];
        $params = MessageCancelParams::newBuilder()
            ->withId('id')
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $callback = function(RequestInterface $request) use($object, $params, $requestOptions) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals(sprintf('%s/messages/%s/cancel', $requestOptions->getBaseUri(), $params->id), $request->getUri());

            return new Response(200, [], json_encode($object));
        };
        $this->httpClient->on($this->matcher, $callback);

        $message = Message::cancel($params, $requestOptions);

        $this->assertEquals($object['object'], $message->getObject());
    }

    /**
     * @test
     */
    public function serviceIdを出力できる()
    {
        $value = 1;
        $message = new Message();
        $message->setProperty('service_id', $value);

        $this->assertEquals($value, $message->getServiceId());
    }

    /**
     * @test
     */
    public function billingAddressIdを出力できる()
    {
        $value = 1;
        $message = new Message();
        $message->setProperty('billing_address_id', $value);

        $this->assertEquals($value, $message->getBillingAddressId());
    }

    /**
     * @test
     */
    public function toを出力できる()
    {
        $value = '1234567890';
        $message = new Message();
        $message->setProperty('to', $value);

        $this->assertEquals($value, $message->getTo());
    }

    /**
     * @test
     */
    public function bodyを出力できる()
    {
        $value = 'body';
        $message = new Message();
        $message->setProperty('body', $value);

        $this->assertEquals($value, $message->getBody());
    }

    /**
     * @test
     */
    public function tagsを出力できる()
    {
        $value = array('tag');
        $message = new Message();
        $message->setProperty('tags', $value);

        $this->assertEquals(json_encode($value), json_encode($message->getTags()));
    }

    /**
     * @test
     */
    public function statusを出力できる()
    {
        $value = 'done';
        $message = new Message();
        $message->setProperty('status', $value);

        $this->assertEquals($value, $message->getStatus());
    }

    /**
     * @test
     */
    public function APIバージョン20230101ではisShortenClickedはnullが出力される()
    {
        // APIバージョン2023-01-01ではnullが返ってくる
        $value = null;
        $message = new Message();
        $message->setProperty('is_shorten_clicked', null);

        $this->assertEquals($value, $message->isShortenClicked());
    }

    /**
     * @test
     */
    public function APIバージョン20231201ではisShortenClickedはbooleanが出力される()
    {
        // APIバージョン2023-12-01ではbooleanが返ってくる
        $value = true;
        $message = new Message();
        $message->setProperty('is_shorten_clicked', $value);

        $this->assertEquals($value, $message->isShortenClicked());
    }

    /**
     * @test
     */
    public function resultを出力できる()
    {
        $value = 'none';
        $message = new Message();
        $message->setProperty('result', $value);

        $this->assertEquals($value, $message->getResult());
    }

    /**
     * @test
     */
    public function sentResultを出力できる()
    {
        $value = 'none';
        $message = new Message();
        $message->setProperty('sent_result', $value);

        $this->assertEquals($value, $message->getSentResult());
    }

    /**
     * @test
     */
    public function carrierを出力できる()
    {
        $value = 'docomo';
        $message = new Message();
        $message->setProperty('carrier', $value);

        $this->assertEquals($value, $message->getCarrier());
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
    public function scheduledAtを出力できる($value)
    {
        $message = new Message();
        $message->setProperty('scheduled_at', $value);
        
        $this->assertEquals($value, $message->getScheduledAt() ? $message->getScheduledAt()->format(DateTimeInterface::ATOM) : null);
    }

    /**
     * @test
     * @dataProvider dateProvider
     */
    public function limitedAtを出力できる($value)
    {
        $message = new Message();
        $message->setProperty('limited_at', $value);
        
        $this->assertEquals($value, $message->getLimitedAt() ? $message->getLimitedAt()->format(DateTimeInterface::ATOM) : null);
    }

    /**
     * @test
     * @dataProvider dateProvider
     */
    public function sentAtを出力できる($value)
    {
        $message = new Message();
        $message->setProperty('sent_at', $value);

        $this->assertEquals($value, $message->getSentAt() ? $message->getSentAt()->format(DateTimeInterface::ATOM) : null);
    }

    /**
     * @test
     * @dataProvider dateProvider
     */
    public function receivedAtを出力できる($value)
    {
        $message = new Message();
        $message->setProperty('received_at', $value);

        $this->assertEquals($value, $message->getReceivedAt() ? $message->getReceivedAt()->format(DateTimeInterface::ATOM) : null);
    }

    /**
     * @test
     * @dataProvider dateProvider
     */
    public function chargedAtを出力できる($value)
    {
        $message = new Message();
        $message->setProperty('charged_at', $value);

        $this->assertEquals($value, $message->getChargedAt() ? $message->getChargedAt()->format(DateTimeInterface::ATOM) : null);
    }

    /**
     * @test
     * @dataProvider dateProvider
     */
    public function createdAtを出力できる($value)
    {
        $message = new Message();
        $message->setProperty('created_at', $value);

        $this->assertEquals($value, $message->getCreatedAt() ? $message->getCreatedAt()->format(DateTimeInterface::ATOM) : null);
    }

    /**
     * @test
     * @dataProvider dateProvider
     */
    public function updatedAtを出力できる($value)
    {
        $message = new Message();
        $message->setProperty('updated_at', $value);

        $this->assertEquals($value, $message->getUpdatedAt() ? $message->getUpdatedAt()->format(DateTimeInterface::ATOM) : null);
    }
}
