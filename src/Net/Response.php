<?php

namespace Karaden\Net;

use Karaden\Model\KaradenObject;
use Karaden\Model\Error;
use Karaden\Exception\UnauthorizedException;
use Karaden\Exception\KaradenException;
use Karaden\Exception\BadRequestException;
use Karaden\Exception\ForbiddenException;
use Karaden\Exception\NotFoundException;
use Karaden\Exception\TooManyRequestsException;
use Karaden\Exception\UnexpectedValueException;
use Karaden\Exception\UnknownErrorException;
use Karaden\Exception\UnprocessableEntityException;
use Karaden\RequestOptions;
use Karaden\Utility;


class Response implements ResponseInterface
{
    protected ?KaradenObject $object = null;
    protected ?KaradenException $error = null;

    const errors = [
        BadRequestException::STATUS_CODE => BadRequestException::class,
        UnauthorizedException::STATUS_CODE => UnauthorizedException::class,
        NotFoundException::STATUS_CODE => NotFoundException::class,
        ForbiddenException::STATUS_CODE => ForbiddenException::class,
        UnprocessableEntityException::STATUS_CODE => UnprocessableEntityException::class,
        TooManyRequestsException::STATUS_CODE => TooManyRequestsException::class,
    ];

    public function isError(): bool
    {
        return $this->error !== null;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function __construct(\Psr\Http\Message\ResponseInterface $response, RequestOptions $requestOptions)
    {
        $this->interpret($response, $requestOptions);
    }

    protected function interpret(\Psr\Http\Message\ResponseInterface $response, RequestOptions $requestOptions)
    {
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $contents = json_decode($body);
        $error = json_last_error();
        if (null === $contents && JSON_ERROR_NONE !== $error) {
            $headers = $response->getHeaders();
            $this->error = new UnexpectedValueException($statusCode, $headers, $body);
            return;
        }

        $object = Utility::convertToKaradenObject($contents, $requestOptions);
        if (200 > $statusCode || 400 <= $statusCode) {
            $headers = $response->getHeaders();
            $this->error = $object->getObject() == 'error' ?
                $this->handleError($statusCode, $headers, $body, $object) :
                new UnexpectedValueException($statusCode, $headers, $body);
        }

        $this->object = $object;
    }

    protected function handleError(int $statusCode, array $headers, string $body, Error $error): KaradenException
    {
        if (array_filter(static::errors, fn($class) => $class::STATUS_CODE == $statusCode)) {
            $class = static::errors[$statusCode];
            return new $class($headers, $body, $error);
        } else {
            return new UnknownErrorException($statusCode, $headers, $body, $error);
        }
    }
}
