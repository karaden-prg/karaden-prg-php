<?php

namespace Karaden\Model;

use Karaden\Net\Requestor;
use Karaden\Net\ResponseInterface;
use Karaden\RequestOptions;

class Requestable extends KaradenObject
{
    public static ?Requestor $requestor = null;

    public static function request(string $method, string $path, ?string $contentType = null, ?array $params = null, ?array $data = null, ?RequestOptions $requestOptions = null): KaradenObject
    {
        $response = (static::$requestor)($method, $path, $contentType, $params, $data, $requestOptions);
        if ($response->isError()) {
            throw $response->getError();
        }
        return $response->getObject();
    }

    public static function requestAndReturnResponseInterface(string $method, string $path, ?string $contentType = null, ?array $params = null, ?array $data = null, ?RequestOptions $requestOptions = null): ResponseInterface
    {
        $response = (static::$requestor)($method, $path, $contentType, $params, $data, $requestOptions, true, false);
        if ($response->isError()) {
            throw $response->getError();
        }
        return $response;
    }
}


Requestable::$requestor = new Requestor();
