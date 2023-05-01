<?php

namespace Karaden\Model;

class Collection extends KaradenObject
{
    const OBJECT_NAME = 'list';

    public function getData(): array
    {
        return $this->getProperty('data');
    }

    public function hasMore(): bool
    {
        return $this->getProperty('has_more');
    }
}
