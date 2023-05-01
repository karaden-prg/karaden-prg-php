<?php

namespace Karaden\Net;

use Karaden\Config;
use Karaden\RequestOptions;
use Karaden\Utility;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\DecoderPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Client\Common\Plugin\RedirectPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Message\Authentication\Bearer;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\NullLogger;

class Requestor implements RequestorInterface
{
    const DEFAULT_USER_AGENT = 'Karaden/PHP/';

    protected ?HttpClient $httpClient = null;
    protected RequestFactoryInterface $requestFactory;
    protected StreamFactoryInterface $streamFactory;

    public function __construct()
    {
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();
    }

    public function __invoke(string $method, string $path, ?string $contentType = null, ?array $params = null, ?array $data = null, ?RequestOptions $requestOptions = null): ResponseInterface
    {
        $requestOptions = Config::asRequestOptions()->merge($requestOptions);
        $requestOptions->validate();

        $headers = [
            'User-Agent' => $this->buildUserAgent($requestOptions),
            'Karaden-Client-User-Agent' => $this->buildClientUserAgent(),
            'Karaden-Version' => $requestOptions->apiVersion,
            'Content-Type' => $contentType,
        ];

        $plugins = [
            new BaseUriPlugin(Psr17FactoryDiscovery::findUriFactory()->createUri($requestOptions->getBaseUri())),
            new HeaderDefaultsPlugin(array_filter($headers, fn($value, $key) => $value, ARRAY_FILTER_USE_BOTH)),
            new AuthenticationPlugin(new Bearer($requestOptions->apiKey)),
            new LoggerPlugin(Config::$logger ?? new NullLogger(), Config::$formatter),
            new DecoderPlugin(),
            new RedirectPlugin(),
        ];
        $httpClient = new PluginClient($this->httpClient ?? Config::$httpClient ?? HttpClientDiscovery::find(), $plugins);

        $request = $this->requestFactory->createRequest($method, $path . $this->buildQuery($params));
        if (null !== $data) {
            $request = $request->withBody($this->streamFactory->createStream($this->buildBody($data)));
        }

        $response = $httpClient->sendRequest($request);
        return new Response($response, $requestOptions);
    }

    protected function buildQuery(?array $params): string
    {
        return $params ? '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986) : '';
    }

    protected function buildBody(?array $data): string
    {
        return $data ? http_build_query($data) : '';
    }

    protected function buildUserAgent(RequestOptions $requestOptions): string
    {
        return $requestOptions->userAgent ? $requestOptions->userAgent : static::DEFAULT_USER_AGENT . Config::VERSION;
    }

    protected function buildClientUserAgent(): string
    {
        return json_encode([
            'bindings_version' => Config::VERSION,
            'language' => 'PHP',
            'language_version' => PHP_VERSION,
            'uname' => Utility::isDisabled(ini_get('disable_functions'), 'php_uname') ? '(disabled)' : php_uname(),
        ]);
    }
}
