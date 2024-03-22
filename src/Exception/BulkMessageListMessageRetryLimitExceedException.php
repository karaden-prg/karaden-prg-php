<?php

namespace Karaden\Exception;

class BulkMessageListMessageRetryLimitExceedException extends KaradenException
{
    public function __construct()
    {
        parent::__construct(null, null);
    }
}
