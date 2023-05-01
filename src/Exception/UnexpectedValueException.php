<?php

namespace Karaden\Exception;

class UnexpectedValueException extends KaradenException
{
    public int $statusCode;

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function __construct(int $statusCode, array $headers, string $body)
    {
        $this->statusCode = $statusCode;
        parent::__construct($headers, $body);
    }
}
