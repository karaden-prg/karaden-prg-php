<?php

namespace Karaden\Net;

use Karaden\RequestOptions;

interface RequestorInterface
{
    function __invoke(string $method, string $path, ?string $contentType = null, ?array $params = null, ?array $data = null, ?RequestOptions $requestOptions = null, bool $isNoContents = false, bool $allowRedirects = true): ResponseInterface;
}
