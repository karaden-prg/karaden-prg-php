<?php

namespace Karaden\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Karaden\Param\Message\Bulk\BulkMessageCreateParams;
use Karaden\Param\Message\Bulk\BulkMessageListMessageParams;
use Karaden\Param\Message\Bulk\BulkMessageShowParams;
use Karaden\RequestOptions;

class BulkMessage extends Requestable
{
    const OBJECT_NAME = 'bulk_message';
    const STATUS_DONE = 'done';
    const STATUS_WAITING = 'waiting';
    const STATUS_PROCESSING = 'processing';
    const STATUS_ERROR = 'error';

    public function getStatus(): string
    {
        return $this->getProperty('status');
    }

    public function getError(): ?Error
    {
        return $this->getProperty('error');
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        $createdAt = $this->getProperty('created_at');
        return $createdAt ? DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $createdAt) : null;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        $updatedAt = $this->getProperty('updated_at');
        return $updatedAt ? DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $updatedAt) : null;
    }

    public static function create(BulkMessageCreateParams $params, ?RequestOptions $requestOptions = null): BulkMessage
    {
        $params->validate();
        return static::request('POST', $params->toPath(), 'application/x-www-form-urlencoded', null, $params->toData(), $requestOptions);
    }

    public static function show(BulkMessageShowParams $params, ?RequestOptions $requestOptions = null): BulkMessage
    {
        $params->validate();
        return static::request('GET', $params->toPath(), null, null, null, $requestOptions);
    }

    public static function listMessage(BulkMessageListMessageParams $params, ?RequestOptions $requestOptions = null): ?string
    {
        $params->validate();
        $response =  static::requestAndReturnResponseInterface('GET', $params->toPath(), null, null, null, $requestOptions);
        return $response->getStatusCode() == 302 ? $response->getHeaders()['location'][0] : null;
    }
}
