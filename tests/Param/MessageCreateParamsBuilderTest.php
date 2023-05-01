<?php

namespace Karaden;

use Karaden\Param\Message\MessageCreateParamsBuilder;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

class MessageCreateParamsBuilderTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /**
     * @test
     */
    public function serviceIdを入力できる()
    {
        $expected = 1;
        $params = (new MessageCreateParamsBuilder())
            ->withServiceId($expected)
            ->build();

        $this->assertEquals($expected, $params->serviceId);
    }

    /**
     * @test
     */
    public function toを入力できる()
    {
        $expected = 'to';
        $params = (new MessageCreateParamsBuilder())
            ->withTo($expected)
            ->build();

        $this->assertEquals($expected, $params->to);
    }

    /**
     * @test
     */
    public function bodyを入力できる()
    {
        $expected = 'body';
        $params = (new MessageCreateParamsBuilder())
            ->withBody($expected)
            ->build();

        $this->assertEquals($expected, $params->body);
    }

    /**
     * @test
     */
    public function tagsを入力できる()
    {
        $expected = ['tags'];
        $params = (new MessageCreateParamsBuilder())
            ->withTags($expected)
            ->build();

        $this->assertEquals(json_encode($expected), json_encode($params->tags));
    }

    /**
     * @test
     */
    public function isShortenを入力できる()
    {
        $expected = true;
        $params = (new MessageCreateParamsBuilder())
            ->withIsShorten($expected)
            ->build();

        $this->assertEquals($expected, $params->isShorten);
    }

    /**
     * @test
     */
    public function scheduledAtを入力できる()
    {
        $expected = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2022-12-10T00:00:00+09:00');
        $params = (new MessageCreateParamsBuilder())
            ->withScheduledAt($expected)
            ->build();

        $this->assertEquals($expected, $params->scheduledAt);
    }

    /**
     * @test
     */
    public function limitedAtを入力できる()
    {
        $expected = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2022-12-10T00:00:00+09:00');
        $params = (new MessageCreateParamsBuilder())
            ->withLimitedAt($expected)
            ->build();

        $this->assertEquals($expected, $params->limitedAt);
    }
}
