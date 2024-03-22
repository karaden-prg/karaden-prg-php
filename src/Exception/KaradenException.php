<?php

namespace Karaden\Exception;

use Karaden\Model\Error;
use Exception;
use Throwable;

abstract class KaradenException extends Exception
{
    public ?array $headers;
    public ?string $body;
    public ?Error $error;

    public function __construct(?array $headers, ?string $body, ?Error $error = null, ?Throwable $previous = null)
    {
        parent::__construct('', 0, $previous);
        $this->headers = $headers;
        $this->body = $body;
        $this->error = $error;
    }
}
