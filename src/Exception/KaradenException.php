<?php

namespace Karaden\Exception;

use Karaden\Model\Error;
use Exception;

abstract class KaradenException extends Exception
{
    public ?array $headers;
    public ?string $body;
    public ?Error $error;

    public function __construct(?array $headers, ?string $body, ?Error $error = null)
    {
        $this->headers = $headers;
        $this->body = $body;
        $this->error = $error;
    }
}
