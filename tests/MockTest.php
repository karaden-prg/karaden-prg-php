<?php

namespace Karaden;

use DateTimeImmutable;
use DateTimeInterface;
use Karaden\Model\Message;
use Karaden\Param\Message\MessageCancelParams;
use Karaden\Param\Message\MessageCreateParams;
use Karaden\Param\Message\MessageDetailParams;
use Karaden\Param\Message\MessageListParams;
use PHPUnit\Framework\TestCase;

/**
 */
class MockTest extends TestCase
{
    /**
     * @test
     */
    public function 一覧()
    {
        $datetime = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2020-01-31T23:59:59+09:00');
        $params = MessageListParams::newBuilder()
            ->withServiceId(1)
            ->withStatus('done')
            ->withStartAt($datetime)
            ->withEndAt($datetime)
            ->withPage(0)
            ->withPerPage(100)
            ->withTag('string')
            ->withResult('done')
            ->withSentResult('none')
            ->withTo('09012345678')
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();
        $messages = Message::list($params, $requestOptions);

        $this->assertEquals('list', $messages->getObject());
        $this->assertEquals(true, $messages->hasMore());
        $messages = $messages->getData();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertEquals('82bdf9de-a532-4bf5-86bc-c9a1366e5f4a', $message->getId());
        $this->assertEquals('message', $message->getObject());
        $this->assertEquals(1, $message->getServiceId());
        $this->assertEquals(1, $message->getBillingAddressId());
        $this->assertEquals('09012345678', $message->getTo());
        $this->assertEquals('本文', $message->getBody());
        $tags = $message->getTags();
        $this->assertIsArray($tags);
        $this->assertCount(1, $tags);
        $this->assertEquals('string', $tags[0]);
        $this->assertEquals(true, $message->isShorten());
        $this->assertEquals('done', $message->getResult());
        $this->assertEquals('done', $message->getStatus());
        $this->assertEquals('none', $message->getSentResult());
        $this->assertEquals('docomo', $message->getCarrier());
        $this->assertEquals($datetime, $message->getScheduledAt());
        $this->assertEquals($datetime, $message->getLimitedAt());
        $this->assertEquals($datetime, $message->getSentAt());
        $this->assertEquals($datetime, $message->getReceivedAt());
        $this->assertEquals($datetime, $message->getChargedAt());
        $this->assertEquals($datetime, $message->getCreatedAt());
        $this->assertEquals($datetime, $message->getUpdatedAt());
    }

    /**
     * @test
     */
    public function 作成（送信）()
    {
        $datetime = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2020-01-31T23:59:59+09:00');
        $params = MessageCreateParams::newBuilder()
            ->withServiceId(1)
            ->withTo('09012345678')
            ->withBody('本文')
            ->withIsShorten(true)
            ->withLimitedAt($datetime)
            ->withScheduledAt($datetime)
            ->withTags(['タグ１', 'タグ２', 'タグ３'])
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();
        $message = Message::create($params, $requestOptions);

        $this->assertEquals('82bdf9de-a532-4bf5-86bc-c9a1366e5f4a', $message->getId());
        $this->assertEquals('message', $message->getObject());
        $this->assertEquals(1, $message->getServiceId());
        $this->assertEquals(1, $message->getBillingAddressId());
        $this->assertEquals('09012345678', $message->getTo());
        $this->assertEquals('本文', $message->getBody());
        $tags = $message->getTags();
        $this->assertIsArray($tags);
        $this->assertCount(1, $tags);
        $this->assertEquals('string', $tags[0]);
        $this->assertEquals(true, $message->isShorten());
        $this->assertEquals('done', $message->getResult());
        $this->assertEquals('done', $message->getStatus());
        $this->assertEquals('none', $message->getSentResult());
        $this->assertEquals('docomo', $message->getCarrier());
        $this->assertEquals($datetime, $message->getScheduledAt());
        $this->assertEquals($datetime, $message->getLimitedAt());
        $this->assertEquals($datetime, $message->getSentAt());
        $this->assertEquals($datetime, $message->getReceivedAt());
        $this->assertEquals($datetime, $message->getChargedAt());
        $this->assertEquals($datetime, $message->getCreatedAt());
        $this->assertEquals($datetime, $message->getUPdatedAt());
    }

    /**
     * @test
     */
    public function 詳細()
    {
        $datetime = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2020-01-31T23:59:59+09:00');
        $params = MessageDetailParams::newBuilder()
            ->withId('82bdf9de-a532-4bf5-86bc-c9a1366e5f4a')
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();
        $message = Message::detail($params, $requestOptions);

        $this->assertEquals('82bdf9de-a532-4bf5-86bc-c9a1366e5f4a', $message->getId());
        $this->assertEquals('message', $message->getObject());
        $this->assertEquals(1, $message->getServiceId());
        $this->assertEquals(1, $message->getBillingAddressId());
        $this->assertEquals('09012345678', $message->getTo());
        $this->assertEquals('本文', $message->getBody());
        $tags = $message->getTags();
        $this->assertIsArray($tags);
        $this->assertCount(1, $tags);
        $this->assertEquals('string', $tags[0]);
        $this->assertEquals(true, $message->isShorten());
        $this->assertEquals('done', $message->getResult());
        $this->assertEquals('done', $message->getStatus());
        $this->assertEquals('none', $message->getSentResult());
        $this->assertEquals('docomo', $message->getCarrier());
        $this->assertEquals($datetime, $message->getScheduledAt());
        $this->assertEquals($datetime, $message->getLimitedAt());
        $this->assertEquals($datetime, $message->getSentAt());
        $this->assertEquals($datetime, $message->getReceivedAt());
        $this->assertEquals($datetime, $message->getChargedAt());
        $this->assertEquals($datetime, $message->getCreatedAt());
        $this->assertEquals($datetime, $message->getUPdatedAt());
    }

    /**
     * @test
     */
    public function キャンセル()
    {
        $datetime = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2020-01-31T23:59:59+09:00');
        $params = MessageCancelParams::newBuilder()
            ->withId('82bdf9de-a532-4bf5-86bc-c9a1366e5f4a')
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();
        $message = Message::cancel($params, $requestOptions);

        $this->assertEquals('82bdf9de-a532-4bf5-86bc-c9a1366e5f4a', $message->getId());
        $this->assertEquals('message', $message->getObject());
        $this->assertEquals(1, $message->getServiceId());
        $this->assertEquals(1, $message->getBillingAddressId());
        $this->assertEquals('09012345678', $message->getTo());
        $this->assertEquals('本文', $message->getBody());
        $tags = $message->getTags();
        $this->assertIsArray($tags);
        $this->assertCount(1, $tags);
        $this->assertEquals('string', $tags[0]);
        $this->assertEquals(true, $message->isShorten());
        $this->assertEquals('done', $message->getResult());
        $this->assertEquals('done', $message->getStatus());
        $this->assertEquals('none', $message->getSentResult());
        $this->assertEquals('docomo', $message->getCarrier());
        $this->assertEquals($datetime, $message->getScheduledAt());
        $this->assertEquals($datetime, $message->getLimitedAt());
        $this->assertEquals($datetime, $message->getSentAt());
        $this->assertEquals($datetime, $message->getReceivedAt());
        $this->assertEquals($datetime, $message->getChargedAt());
        $this->assertEquals($datetime, $message->getCreatedAt());
        $this->assertEquals($datetime, $message->getUPdatedAt());
    }
}
 