<?php

namespace Karaden\Exception;

class BulkMessageShowRetryLimitExceedException extends KaradenException
{
    public function __construct()
    {
        parent::__construct(null, null);
    }
}
