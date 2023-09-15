<?php

namespace Karaden;

use Http\Client\HttpClient;
use Http\Message\Formatter;
use Psr\Log\LoggerInterface;

class Config
{
    const VERSION = '1.0.2';
    const DEFAULT_API_BASE = 'https://prg.karaden.jp/api';
    const DEFALUT_API_VERSION = '2023-01-01';

    public static ?HttpClient $httpClient = null;
    public static ?LoggerInterface $logger = null;
    public static ?Formatter $formatter = null;
    public static ?string $apiVersion = self::DEFALUT_API_VERSION;
    public static ?string $apiKey = null;
    public static ?string $tenantId = null;
    public static ?string $userAgent = null;
    public static ?string $apiBase = self::DEFAULT_API_BASE;

    private function __construct()
    {
    } 

    public static function reset()
    {
        self::$httpClient = null;
        self::$logger = null;
        self::$formatter = null;
        self::$apiVersion = self::DEFALUT_API_VERSION;
        self::$apiKey = null;
        self::$tenantId = null;
        self::$userAgent = null;
        self::$apiBase = self::DEFAULT_API_BASE;
    }

    public static function asRequestOptions(): RequestOptions
    {
        return RequestOptions::newBuilder()
            ->withApiVersion(self::$apiVersion)
            ->withApiKey(self::$apiKey)
            ->withTenantId(self::$tenantId)
            ->withUserAgent(self::$userAgent)
            ->withApiBase(self::$apiBase)
            ->build();
    }
}
