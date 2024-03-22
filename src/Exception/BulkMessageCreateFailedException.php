<?php

namespace Karaden\Exception;

class BulkMessageCreateFailedException extends KaradenException
{
    public function __construct()
    {
        parent::__construct(null, null);
    }
}
