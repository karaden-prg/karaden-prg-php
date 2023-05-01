<?php

namespace Karaden\Param\Message;

use DateTimeInterface;

class MessageListParams extends MessageParams
{
    public ?int $serviceId;
    public ?string $to;
    public ?string $status;
    public ?string $result;
    public ?string $sentResult;
    public ?string $tag;
    public ?DateTimeInterface $startAt;
    public ?DateTimeInterface $endAt;
    public ?int $page;
    public ?int $perPage;

    public function __construct(?int $serviceId = null, ?string $to = null, ?string $status = null, ?string $result = null, ?string $sentResult = null, ?string $tag = null, ?DateTimeInterface $startAt = null, ?DateTimeInterface $endAt = null, ?int $page = null, ?int $perPage = null)
    {
        $this->serviceId = $serviceId;
        $this->to = $to;
        $this->status = $status;
        $this->result = $result;
        $this->sentResult = $sentResult;
        $this->tag = $tag;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public function toParams(): array
    {
        return [
            'service_id' => $this->serviceId,
            'to' => $this->to,
            'status' => $this->status,
            'result' => $this->result,
            'sent_result' => $this->sentResult,
            'tag' => $this->tag,
            'start_at' => $this->startAt ? $this->startAt->format(DATE_ATOM) : null,
            'end_at' => $this->endAt ? $this->endAt->format(DATE_ATOM) : null,
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }

    public function toPath(): string
    {
        return static::CONTEXT_PATH;
    }

    public static function newBuilder(): MessageListParamsBuilder
    {
        return new MessageListParamsBuilder();
    }
}
