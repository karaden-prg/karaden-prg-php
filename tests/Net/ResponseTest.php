<?php

namespace Karaden\Net;

use Karaden\Model\KaradenObject;
use Karaden\Exception\UnauthorizedException;
use Karaden\Exception\BadRequestException;
use Karaden\Exception\NotFoundException;
use Karaden\Exception\ForbiddenException;
use Karaden\Exception\TooManyRequestsException;
use Karaden\Exception\UnexpectedValueException;
use Karaden\Exception\UnknownErrorException;
use Karaden\Net\Response;
use Karaden\RequestOptions;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Karaden\Exception\UnprocessableEntityException;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function statusCodeProvider(): array
    {
        return [
            [100],
            [200],
            [300],
            [400],
            [500],
        ];
    }

    public function objectProvider(): array
    {
        return [
            ['message'],
            [''],
            [null],
        ];
    }

    public function specialErrorStatusCodeProvider(): array
    {
        return [
            [UnauthorizedException::class],
            [BadRequestException::class],
            [NotFoundException::class],
            [ForbiddenException::class],
            [UnprocessableEntityException::class],
            [TooManyRequestsException::class],
        ];
    }

    public function errorStatusCodeProvider(): array
    {
        $excluded = [
            UnauthorizedException::STATUS_CODE,
            BadRequestException::STATUS_CODE,
            NotFoundException::STATUS_CODE,
            ForbiddenException::STATUS_CODE,
            UnprocessableEntityException::STATUS_CODE,
            TooManyRequestsException::STATUS_CODE,
        ];
        $statusCodes = array_filter(range(100, 199) + range(400, 599), fn($statusCode) => ! in_array($statusCode, $excluded));
        return array_map(fn($status) => [$status], $statusCodes);
    }

    /**
     * @test
     */
    public function 正常系のステータスコードで本文がJSONならばオブジェクトが返る()
    {
        $statusCode = 200;
        $body = '{"test": "test"}';
        $requestOptions = new RequestOptions();

        $response = new Response(new Psr7Response($statusCode, [], $body), $requestOptions);

        $this->assertFalse($response->isError());
        $this->assertInstanceOf(KaradenObject::class, $response->getObject());
    }

    /**
     * @test
     * @dataProvider statusCodeProvider
     */
    public function ステータスコードによらず本文がJSONでなければUnexpectedValueException(int $statusCode)
    {
        $body = '';
        $requestOptions = new RequestOptions();

        $response = new Response(new Psr7Response($statusCode, [], $body), $requestOptions);

        $this->assertTrue($response->isError());
        $this->assertInstanceOf(UnexpectedValueException::class, $response->getError());
        $this->assertEquals($statusCode, $response->getError()->getStatusCode());
    }

    /**
     * @test
     */
    public function エラー系のステータスコードで本文にobjectのプロパティがなければUnexpectedValueException()
    {
        $statusCode = 400;
        $body = '{"test": "test"}';
        $requestOptions = new requestoptions();

        $response = new Response(new Psr7Response($statusCode, [], $body), $requestOptions);

        $this->assertTrue($response->isError());
        $this->assertInstanceOf(UnexpectedValueException::class, $response->getError());
        $this->assertEquals($statusCode, $response->getError()->getStatusCode());
    }

    /**
     * @test
     * @dataProvider objectProvider
     */
    public function エラー系のステータスコードで本文にobjectのプロパティの値がerror以外はUnexpectedValueException($object)
    {
        $statusCode = 400;
        $body = sprintf('{"object": "%s"}', $object);
        $requestOptions = new RequestOptions();

        $response = new Response(new Psr7Response($statusCode, [], $body), $requestOptions);

        $this->assertTrue($response->isError());
        $this->assertInstanceOf(UnexpectedValueException::class, $response->getError());
        $this->assertEquals($statusCode, $response->getError()->getStatusCode());
    }

    /**
     * @test
     * @dataProvider errorStatusCodeProvider
     */
    public function エラー系のステータスコードで特殊例外以外はUnknownErrorException($statusCode)
    {
        $body = '{"object": "error", "test": "test"}';
        $requestOptions = new RequestOptions();

        $response = new Response(new Psr7Response($statusCode, [], $body), $requestOptions);

        $this->assertTrue($response->isError());
        $this->assertInstanceOf(UnknownErrorException::class, $response->getError());
        $this->assertEquals($statusCode, $response->getError()->getStatusCode());
    }


    /**
     * @test
     * @dataProvider specialErrorStatusCodeProvider
     */
    public function 特殊例外のステータスコード($class)
    {
        $statsuCode = $class::STATUS_CODE;
        $body = '{"object": "error", "test": "test"}';
        $requestOptions = new RequestOptions();

        $response = new Response(new Psr7Response($statsuCode, [], $body), $requestOptions);

        $this->assertTrue($response->isError());
        $this->assertInstanceOf($class, $response->getError());
    }
}
