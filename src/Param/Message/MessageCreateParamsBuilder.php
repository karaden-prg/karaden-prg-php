<?php

namespace Karaden\Param\Message;

use DateTimeInterface;

class MessageCreateParamsBuilder
{
    protected MessageCreateParams $params;

    public function __construct()
    {
        $this->params = new MessageCreateParams(0, '', '');
    }

    public function withServiceId(int $serviceId): self
    {
        $this->params->serviceId = $serviceId;
        return $this;
    }

    public function withTo(string $to): self
    {
        $this->params->to = $to;
        return $this;
    }

    public function withBody(string $body): self
    {
        $this->params->body = $body;
        return $this;
    }

    public function withTags(array $tags): self
    {
        $this->params->tags = $tags;
        return $this;
    }

    public function withIsShorten(bool $isShorten): self
    {
        $this->params->isShorten = $isShorten;
        return $this;
    }

    public function withScheduledAt(DateTimeInterface $scheduledAt): self
    {
        $this->params->scheduledAt = $scheduledAt;
        return $this;
    }

    public function withLimitedAt(DateTimeInterface $limitedAt): self
    {
        $this->params->limitedAt = $limitedAt;
        return $this;
    }

    public function build(): MessageCreateParams
    {
        return clone $this->params;
    }
}
