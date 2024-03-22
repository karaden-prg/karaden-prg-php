<?php

namespace Karaden\Exception;

class FileDownloadFailedException extends KaradenException
{
    public function __construct()
    {
        parent::__construct(null, null);
    }
}
