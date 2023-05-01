<?php

namespace Karaden;

use Karaden\Param\Message\MessageListParams;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

class MessageListParamsTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /**
     * @test
     */
    public function 正しいパスを生成できる()
    {
        $params = new MessageListParams();

        $this->assertEquals(MessageListParams::CONTEXT_PATH, $params->toPath());
    }

    /**
     * @test
     */
    public function serviceIdをクエリにできる()
    {
        $expected = 1;
        $params = new MessageListParams($expected);

        $actual = $params->toParams();
        $this->assertEquals($expected, $actual['service_id']);
    }

    /**
     * @test
     */
    public function toをクエリにできる()
    {
        $expected = 'to';
        $params = new MessageListParams(null, $expected);

        $actual = $params->toParams();
        $this->assertEquals($expected, $actual['to']);
    }

    /**
     * @test
     */
    public function statusをクエリにできる()
    {
        $expected = 'status';
        $params = new MessageListParams(null, null, $expected);

        $actual = $params->toParams();
        $this->assertEquals($expected, $actual['status']);
    }

    /**
     * @test
     */
    public function resultをクエリにできる()
    {
        $expected = 'result';
        $params = new MessageListParams(null, null, null, $expected);

        $actual = $params->toParams();
        $this->assertEquals($expected, $actual['result']);
    }

    /**
     * @test
     */
    public function sentResultをクエリにできる()
    {
        $expected = 'sentResult';
        $params = new MessageListParams(null, null, null, null, $expected);

        $actual = $params->toParams();
        $this->assertEquals($expected, $actual['sent_result']);
    }

    /**
     * @test
     */
    public function tagをクエリにできる()
    {
        $expected = 'tag';
        $params = new MessageListParams(null, null, null, null, null, $expected);

        $actual = $params->toParams();
        $this->assertEquals($expected, $actual['tag']);
    }

    /**
     * @test
     */
    public function startAtをクエリにできる()
    {
        $expected = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2022-12-10T00:00:00+09:00');
        $params = new MessageListParams(null, null, null, null, null, null, $expected);

        $actual = $params->toParams();
        $this->assertEquals($expected->format(DATE_ATOM), $actual['start_at']);
    }

    /**
     * @test
     */
    public function endAtをクエリにできる()
    {
        $expected = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2022-12-10T00:00:00+09:00');
        $params = new MessageListParams(null, null, null, null, null, null, null, $expected);

        $actual = $params->toParams();
        $this->assertEquals($expected->format(DATE_ATOM), $actual['end_at']);
    }

    /**
     * @test
     */
    public function pageをクエリにできる()
    {
        $expected = 1;
        $params = new MessageListParams(null, null, null, null, null, null, null, null, $expected);

        $actual = $params->toParams();
        $this->assertEquals($expected, $actual['page']);
    }

    /**
     * @test
     */
    public function perPageをクエリにできる()
    {
        $expected = 1;
        $params = new MessageListParams(null, null, null, null, null, null, null, null, null, $expected);

        $actual = $params->toParams();
        $this->assertEquals($expected, $actual['per_page']);
    }
}
