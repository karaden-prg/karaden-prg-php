<?php

namespace Karaden\Exception;

use Karaden\Model\Error;

class UnknownErrorException extends KaradenException
{
    public int $statusCode;

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function __construct(int $statusCode, array $headers, string $body, Error $error)
    {
        $this->statusCode = $statusCode;
        parent::__construct($headers, $body, $error);
    }
}
