<?php

namespace Karaden\Param\Message\Bulk;

abstract class BulkMessageParams
{
    const CONTEXT_PATH = '/messages/bulks';

    function validate(): BulkMessageParams { return $this; }
}
