<?php

namespace Karaden\Param\Message\Bulk;

class BulkMessageListMessageParamsBuilder
{
    protected BulkMessageListMessageParams $params;

    public function __construct()
    {
        $this->params = new BulkMessageListMessageParams('');
    }

    public function withId(string $id): self
    {
        $this->params->id = $id;
        return $this;
    }

    public function build(): BulkMessageListMessageParams
    {
        return clone $this->params;
    }
}
