<?php

namespace Karaden\Param\Message;

use DateTimeInterface;

class MessageListParamsBuilder
{
    protected MessageListParams $params;

    public function __construct()
    {
        $this->params = new MessageListParams();
    }

    public function withServiceId(int $serviceId)
    {
        $this->params->serviceId = $serviceId;
        return $this;
    }

    public function withTo(string $to)
    {
        $this->params->to = $to;
        return $this;
    }

    public function withStatus(string $status)
    {
        $this->params->status = $status;
        return $this;
    }

    public function withResult(string $result)
    {
        $this->params->result = $result;
        return $this;
    }

    public function withSentResult(string $sentResult)
    {
        $this->params->sentResult = $sentResult;
        return $this;
    }

    public function withTag(string $tag)
    {
        $this->params->tag = $tag;
        return $this;
    }

    public function withStartAt(DateTimeInterface $startAt)
    {
        $this->params->startAt = $startAt;
        return $this;
    }

    public function withEndAt(DateTimeInterface $endAt)
    {
        $this->params->endAt = $endAt;
        return $this;
    }

    public function withPage(int $page)
    {
        $this->params->page = $page;
        return $this;
    }

    public function withPerPage(int $perPage)
    {
        $this->params->perPage = $perPage;
        return $this;
    }

    public function build(): MessageListParams
    {
        return clone $this->params;
    }
}
