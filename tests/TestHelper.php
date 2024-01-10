<?php

namespace Karaden;

use Karaden\Net\Requestor;
use Http\Discovery\HttpClientDiscovery;
use Http\Mock\Client;
use ReflectionClass;


class TestHelper
{
    const API_BASE = 'http://localhost:4010';
    const API_KEY = '123';
    const TENANT_ID = '159bfd33-b9b7-f424-4755-c119b324591d';
    const API_VERSION = '2023-12-01';

    public static function getDefaultRequestOptionsBuilder(): RequestOptionsBuilder
    {
        return RequestOptions::newBuilder()
            ->withApiBase(self::API_BASE)
            ->withApiKey(self::API_KEY)
            ->withTenantId(self::TENANT_ID)
            ->withApiVersion(self::API_VERSION);
    }

    public static function getRequestableHttpClient(string $class): Client
    {
        $reflection = new ReflectionClass($class);
        $requestor = $reflection->getStaticPropertyValue('requestor');
        return static::getHttpClient($requestor);
    }

    public static function getHttpClient(Requestor $request): Client
    {
        $httpClient = HttpClientDiscovery::find();
        $property = static::setProtectedProperty($request, 'httpClient', $httpClient);
        return $httpClient;
    }

    public static function setProtectedProperty(object $object, string $name, $value)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
