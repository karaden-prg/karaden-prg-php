<?php

namespace Karaden\Param\Message\Bulk;

class BulkMessageShowParamsBuilder
{
    protected BulkMessageShowParams $params;

    public function __construct()
    {
        $this->params = new BulkMessageShowParams('');
    }

    public function withId(string $id): self
    {
        $this->params->id = $id;
        return $this;
    }

    public function build(): BulkMessageShowParams
    {
        return clone $this->params;
    }
}
