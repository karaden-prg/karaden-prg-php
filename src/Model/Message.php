<?php

namespace Karaden\Model;

use Karaden\Param\Message\MessageCancelParams;
use Karaden\Param\Message\MessageCreateParams;
use Karaden\Param\Message\MessageDetailParams;
use Karaden\Param\Message\MessageListParams;
use Karaden\RequestOptions;
use DateTimeImmutable;
use DateTimeInterface;

class Message extends Requestable
{
    const OBJECT_NAME = 'message';

    public function getServiceId(): int
    {
        return $this->getProperty('service_id');
    }

    public function getBillingAddressId(): int
    {
        return $this->getProperty('billing_address_id');
    }

    public function getTo(): string
    {
        return $this->getProperty('to');
    }

    public function getBody(): string
    {
        return $this->getProperty('body');
    }

    public function getTags(): array
    {
        return $this->getProperty('tags');
    }

    public function isShorten(): bool
    {
        return $this->getProperty('is_shorten');
    }

    public function getResult(): string
    {
        return $this->getProperty('result');
    }

    public function getStatus(): string
    {
        return $this->getProperty('status');
    }

    public function getSentResult(): string
    {
        return $this->getProperty('sent_result');
    }

    public function getCarrier(): string
    {
        return $this->getProperty('carrier');
    }

    public function getChargedCountPerSent(): int
    {
        return $this->getProperty('charged_count_per_sent');
    }

    public function getScheduledAt(): ?DateTimeInterface
    {
        $scheduledAt = $this->getProperty('scheduled_at');
        return $scheduledAt ? DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $scheduledAt) : null;
    }

    public function getLimitedAt(): ?DateTimeInterface
    {
        $limitedAt = $this->getProperty("limited_at");
        return $limitedAt ? DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $limitedAt) : null;
    }

    public function getSentAt(): ?DateTimeInterface
    {
        $sentAt = $this->getProperty("sent_at");
        return $sentAt ? DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $sentAt) : null;
    }

    public function getReceivedAt(): ?DateTimeInterface
    {
        $receivedAt = $this->getProperty('received_at');
        return $receivedAt ? DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $receivedAt) : null;
    }

    public function getChargedAt(): ?DateTimeInterface
    {
        $chargedAt = $this->getProperty('charged_at');
        return $chargedAt ? DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $chargedAt) : null;
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

    public static function create(MessageCreateParams $params, ?RequestOptions $requestOptions = null): Message
    {
        $params->validate();
        return static::request('POST', $params->toPath(), 'application/x-www-form-urlencoded', null, $params->toData(), $requestOptions);
    }

    public static function detail(MessageDetailParams $params, ?RequestOptions $requestOptions = null): Message
    {
        $params->validate();
        return static::request('GET', $params->toPath(), null, null, null, $requestOptions);
    }

    public static function list(MessageListParams $params, ?RequestOptions $requestOptions = null): Collection
    {
        $params->validate();
        return static::request('GET', $params->toPath(), null, $params->toParams(), null, $requestOptions);
    }

    public static function cancel(MessageCancelParams $params, ?RequestOptions $requestOptions = null): Message
    {
        $params->validate();
        return static::request('POST', $params->toPath(), null, null, null, $requestOptions);
    }
}
