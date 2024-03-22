<?php

namespace Karaden\Param\Message\Bulk;

class BulkMessageDownloadParamsBuilder
{
    protected BulkMessageDownloadParams $params;

    public function __construct()
    {
        $this->params = new BulkMessageDownloadParams('', '');
    }

    public function withId(string $id): self
    {
        $this->params->id = $id;
        return $this;
    }

    public function withDirectoryPath(string $directoryPath): self
    {
        $this->params->directoryPath = $directoryPath;
        return $this;
    }

    public function withMaxRetries(int $maxRetries): self
    {
        $this->params->maxRetries = $maxRetries;
        return $this;
    }

    public function withRetryInterval(int $retryInterval): self
    {
        $this->params->retryInterval = $retryInterval;
        return $this;
    }

    public function build(): BulkMessageDownloadParams
    {
        return clone $this->params;
    }
}
