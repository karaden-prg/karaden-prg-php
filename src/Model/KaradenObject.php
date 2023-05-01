<?php

namespace Karaden\Model;

use Karaden\RequestOptions;

class KaradenObject
{
    protected array $properties = [];
    protected ?RequestOptions $requestOptions;

    public function __construct($id = null, ?RequestOptions $requestOptions = null)
    {
        $this->setProperty('id', $id);
        $this->requestOptions = $requestOptions;
    }

    public function getId()
    {
        return $this->getProperty('id');
    }

    public function getObject(): ?string
    {
        return $this->getProperty('object');
    }

    public function getPropertyKeys(): array
    {
        return array_keys($this->properties);
    }

    public function setProperty($key, $value)
    {
        $this->properties[$key] = $value;
    }

    public function getProperty($key)
    {
        return isset($this->properties[$key]) ? $this->properties[$key] : null;
    }
}
