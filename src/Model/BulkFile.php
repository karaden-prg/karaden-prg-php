<?php

namespace Karaden\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Karaden\Param\Message\Bulk\BulkMessageParams;
use Karaden\RequestOptions;

class BulkFile extends Requestable
{
    const OBJECT_NAME = 'bulk_file';

    public function getUrl(): string
    {
        return $this->getProperty('url');
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        $createdAt = $this->getProperty('created_at');
        return $createdAt ? DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $createdAt) : null;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        $expiresAt = $this->getProperty('expires_at');
        return $expiresAt ? DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $expiresAt) : null;
    }

    public static function create(?RequestOptions $requestOptions = null): BulkFile
    {
        $path = sprintf("%s/files", BulkMessageParams::CONTEXT_PATH);
        return static::request('POST', $path, null, null, null, $requestOptions);
    }
}
