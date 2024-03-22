<?php

namespace Karaden\Exception;

use Exception;

class FileUploadFailedException extends KaradenException
{
    public function __construct(?Exception $e = null)
    {
        parent::__construct(null, null, null, $e);
    }
}
