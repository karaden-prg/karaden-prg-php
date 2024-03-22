<?php

namespace Karaden\Exception;

class NotImplementationException extends KaradenException
{
    public function __construct()
    {
        parent::__construct(null, null);
    }
}
