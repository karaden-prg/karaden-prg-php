<?php

namespace Karaden\Exception;

use Karaden\Model\Error;

class InvalidRequestOptionsException extends KaradenException
{
    public function __construct(Error $error)
    {
        parent::__construct(null, null, $error);
    }
}
