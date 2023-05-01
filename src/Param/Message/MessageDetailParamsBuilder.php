<?php

namespace Karaden\Param\Message;


class MessageDetailParamsBuilder
{
    protected MessageDetailParams $params;

    public function __construct()
    {
        $this->params = new MessageDetailParams("");
    }

    public function withId(string $id): self
    {
        $this->params->id = $id;
        return $this;
    }

    public function build(): MessageDetailParams
    {
        return clone $this->params;
    }
}
