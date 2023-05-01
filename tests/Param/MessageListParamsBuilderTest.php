<?php

namespace Karaden;

use Karaden\Param\Message\MessageListParamsBuilder;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

class MessageListParamsBuilderTest extends TestCase
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
        $params = (new MessageListParamsBuilder())
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
        $params = (new MessageListParamsBuilder())
            ->withTo($expected)
            ->build();

        $this->assertEquals($expected, $params->to);
    }

    /**
     * @test
     */
    public function statusを入力できる()
    {
        $expected = 'status';
        $params = (new MessageListParamsBuilder())
            ->withStatus($expected)
            ->build();

        $this->assertEquals($expected, $params->status);
    }

    /**
     * @test
     */
    public function resultを入力できる()
    {
        $expected = 'result';
        $params = (new MessageListParamsBuilder())
            ->withResult($expected)
            ->build();

        $this->assertEquals($expected, $params->result);
    }

    /**
     * @test
     */
    public function sentResultを入力できる()
    {
        $expected = 'sentResult';
        $params = (new MessageListParamsBuilder())
            ->withSentResult($expected)
            ->build();

        $this->assertEquals($expected, $params->sentResult);
    }

    /**
     * @test
     */
    public function tagを入力できる()
    {
        $expected = 'tag';
        $params = (new MessageListParamsBuilder())
            ->withTag($expected)
            ->build();

        $this->assertEquals($expected, $params->tag);
    }

    /**
     * @test
     */
    public function startAtを入力できる()
    {
        $expected = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2022-12-10T00:00:00+09:00');
        $params = (new MessageListParamsBuilder())
            ->withStartAt($expected)
            ->build();

        $this->assertEquals($expected, $params->startAt);
    }

    /**
     * @test
     */
    public function endAtを入力できる()
    {
        $expected = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2022-12-10T00:00:00+09:00');
        $params = (new MessageListParamsBuilder())
            ->withEndAt($expected)
            ->build();

        $this->assertEquals($expected, $params->endAt);
    }

    /**
     * @test
     */
    public function pageを入力できる()
    {
        $expected = 1;
        $params = (new MessageListParamsBuilder())
            ->withPage($expected)
            ->build();

        $this->assertEquals($expected, $params->page);
    }

    /**
     * @test
     */
    public function perPageを入力できる()
    {
        $expected = 1;
        $params = (new MessageListParamsBuilder())
            ->withPerPage($expected)
            ->build();

        $this->assertEquals($expected, $params->perPage);
    }
}
