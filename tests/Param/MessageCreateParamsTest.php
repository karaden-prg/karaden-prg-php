<?php

namespace Karaden;

use Karaden\Param\Message\MessageCreateParams;
use DateTimeImmutable;
use DateTimeInterface;
use Karaden\Exception\InvalidParamsException;
use PHPUnit\Framework\TestCase;

class MessageCreateParamsTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /**
     * @test
     */
    public function 正しいパスを生成できる()
    {
        $params = new MessageCreateParams(0, '', '', null, false, null, null);

        $this->assertEquals(MessageCreateParams::CONTEXT_PATH, $params->toPath());
    }

    /**
     * @test
     */
    public function serviceIdを送信データにできる()
    {
        $expected = 1;
        $params = new MessageCreateParams($expected, '', '', null, false, null, null);

        $actual = $params->toData();
        $this->assertEquals($expected, $actual['service_id']);
    }

    /**
     * @test
     */
    public function toを送信データにできる()
    {
        $expected = 'to';
        $params = new MessageCreateParams(0, $expected, '', null, false, null, null);

        $actual = $params->toData();
        $this->assertEquals($expected, $actual['to']);
    }

    /**
     * @test
     */
    public function bodyを送信データにできる()
    {
        $expected = 'body';
        $params = new MessageCreateParams(0, '', $expected, null, false, null, null);

        $actual = $params->toData();
        $this->assertEquals($expected, $actual['body']);
    }

    /**
     * @test
     */
    public function tagsを送信データにできる()
    {
        $expected = ['tag'];
        $params = new MessageCreateParams(0, '', '', $expected, false, null, null);

        $actual = $params->toData();
        $this->assertEquals(json_encode($expected), json_encode($actual['tags']));
    }

    public function isShortenProvider()
    {
        return [
            [true, 'true'],
            [false, 'false'],
            [null, null],
        ];
    }

    /**
     * @dataProvider isShortenProvider
     * @test
     */
    public function isShortenを送信データにできる($param, $expected)
    {
        $params = new MessageCreateParams(0, '', '', null, $param, null, null);

        $actual = $params->toData();
        $this->assertEquals($expected, $actual['is_shorten']);
    }

    /**
     * @test
     */
    public function scheduledAtを送信データにできる()
    {
        $expected = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2022-12-10T00:00:00+09:00');
        $params = new MessageCreateParams(0, '', '', null, false, $expected, null);

        $actual = $params->toData();
        $this->assertEquals($expected->format(DATE_ATOM), $actual['scheduled_at']);
    }

    /**
     * @test
     */
    public function limitedAtを送信データにできる()
    {
        $expected = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, '2022-12-10T00:00:00+09:00');
        $params = new MessageCreateParams(0, '', '', null, false, null, $expected);

        $actual = $params->toData();
        $this->assertEquals($expected->format(DATE_ATOM), $actual['limited_at']);
    }

    public function serviceIdProvider()
    {
        return [
            [0],
            [-1],
            [null],
        ];
    }

    /**
     * @test
     * @dataProvider serviceIdProvider
     */
    public function serviceIdが空文字や未指定はエラー($serviceId)
    {
        $this->expectException(InvalidParamsException::class);
        try {
            $builder = MessageCreateParams::newBuilder();
            if (! is_null($serviceId)) {
                $builder->withServiceId($serviceId);
            }
            $builder->build()
                ->validate();
        } catch(InvalidParamsException $e) {
            $messages = $e->error->getErrors()->getProperty('serviceId');
            $this->assertIsArray($messages);
            throw $e;
        }
    }

    public function toProvider()
    {
        return [
            [''],
            [null],
        ];
    }

    /**
     * @test
     * @dataProvider toProvider
     */
    public function toが空文字や未指定はエラー($to)
    {
        $this->expectException(InvalidParamsException::class);
        try {
            $builder = MessageCreateParams::newBuilder();
            if (! is_null($to)) {
                $builder->withTo($to);
            }
            $builder->build()
                ->validate();
        } catch(InvalidParamsException $e) {
            $messages = $e->error->getErrors()->getProperty('to');
            $this->assertIsArray($messages);
            throw $e;
        }
    }

    public function bodyProvider()
    {
        return [
            [''],
            [null],
        ];
    }

    /**
     * @test
     * @dataProvider bodyProvider
     */
    public function bodyが空文字や未指定はエラー($body)
    {
        $this->expectException(InvalidParamsException::class);
        try {
            $builder = MessageCreateParams::newBuilder();
            if (! is_null($body)) {
                $builder->withBody($body);
            }
            $builder->build()
                ->validate();
        } catch(InvalidParamsException $e) {
            $messages = $e->error->getErrors()->getProperty('body');
            $this->assertIsArray($messages);
            throw $e;
        }
    }
}
