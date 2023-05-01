<?php

namespace Karaden\Model;

use Karaden\Model\KaradenObject;

class Error extends KaradenObject
{
    const OBJECT_NAME = 'error';

    public function getCode(): string
    {
        return $this->getProperty('code');
    }

    public function getMessage(): string
    {
        return $this->getProperty('message');
    }

    public function getErrors(): KaradenObject
    {
        return $this->getProperty('errors');
    }
}
