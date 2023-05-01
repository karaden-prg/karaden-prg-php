<?php

namespace Karaden\Exception;

use Karaden\Model\Error;

class InvalidParamsException extends KaradenException
{
    public function __construct(Error $error)
    {
        parent::__construct(null, null, $error);
    }
}
