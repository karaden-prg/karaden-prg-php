<?php

namespace Karaden\Param\Message\Bulk;

class BulkMessageCreateParamsBuilder
{
    protected BulkMessageCreateParams $params;

    public function __construct()
    {
        $this->params = new BulkMessageCreateParams('');
    }

    public function withBulkFileId(string $bulkFileId): self
    {
        $this->params->bulkFileId = $bulkFileId;
        return $this;
    }

    public function build(): BulkMessageCreateParams
    {
        return clone $this->params;
    }
}
