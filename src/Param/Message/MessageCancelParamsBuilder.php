<?php

namespace Karaden\Param\Message;


class MessageCancelParamsBuilder
{
    protected MessageCancelParams $params;

    public function __construct()
    {
        $this->params = new MessageCancelParams("");
    }

    public function withId(string $id): self
    {
        $this->params->id = $id;
        return $this;
    }

    public function build(): MessageCancelParams
    {
        return clone $this->params;
    }
}
