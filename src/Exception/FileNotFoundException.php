<?php

namespace Karaden\Exception;

class FileNotFoundException extends KaradenException
{
    public function __construct()
    {
        parent::__construct(null, null);
    }
}
