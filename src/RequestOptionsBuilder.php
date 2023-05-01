<?php

namespace Karaden;

class RequestOptionsBuilder
{
    protected RequestOptions $requestOptions;

    public function __construct()
    {
        $this->requestOptions = new RequestOptions();
    }

    public function withApiVersion(?string $apiVersion): self
    {
        $this->requestOptions->apiVersion = $apiVersion;
        return $this;
    }

    public function withApiKey(?string $apiKey): self
    {
        $this->requestOptions->apiKey = $apiKey;
        return $this;
    }

    public function withTenantId(?string $tenantId): self
    {
        $this->requestOptions->tenantId = $tenantId;
        return $this;
    }

    public function withUserAgent(?string $userAgent): self
    {
        $this->requestOptions->userAgent = $userAgent;
        return $this;
    }

    public function withApiBase(?string $apiBase): self
    {
        $this->requestOptions->apiBase = $apiBase;
        return $this;
    }

    public function build(): RequestOptions
    {
        return clone $this->requestOptions;
    }
}
