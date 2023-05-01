<?php

namespace Karaden;

use Karaden\Exception\InvalidRequestOptionsException;
use Karaden\Model\Error;
use Karaden\Model\KaradenObject;

class RequestOptions
{
    public ?string $apiVersion = null;
    public ?string $apiKey = null;
    public ?string $tenantId = null;
    public ?string $userAgent = null;
    public ?string $apiBase = null;

    public function __construct()
    {
    }

    public function merge(?RequestOptions $source): self
    {
        $destination = clone $this;
        if ($source != null) {
            foreach(get_object_vars($source) as $key=>$value) {
                if ($value) {
                    $destination->$key = $value;
                }
            }
        }
        
        return $destination;
    }

    public function getBaseUri(): string
    {
        return "{$this->apiBase}/{$this->tenantId}";
    }

    protected function validateApiVersion()
    {
        $messages = [];

        if (! $this->apiVersion) {
            $messages[] = 'apiVersionは必須です。';
            $messages[] = '文字列を入力してください。'; 
        }

        return $messages;
    }

    protected function validateApiKey()
    {
        $messages = [];

        if (! $this->apiKey) {
            $messages[] = 'apiKeyは必須です。';
            $messages[] = '文字列を入力してください。'; 
        }

        return $messages;
    }

    protected function validateTenantId()
    {
        $messages = [];

        if (! $this->tenantId) {
            $messages[] = 'tenantIdは必須です。';
            $messages[] = '文字列を入力してください。'; 
        }

        return $messages;
    }

    protected function validateApiBase()
    {
        $messages = [];

        if (! $this->apiBase) {
            $messages[] = 'apiBaseは必須です。';
            $messages[] = '文字列を入力してください。'; 
        }

        return $messages;
    }

    public function validate(): RequestOptions
    {
        $errors = new KaradenObject();
        $hasError = false;

        $messages = $this->validateApiVersion();
        if ($messages) {
            $errors->setProperty('apiVersion', $messages);
            $hasError = true;
        }

        $messages = $this->validateApiKey();
        if ($messages) {
            $errors->setProperty('apiKey', $messages);
            $hasError = true;
        }

        $messages = $this->validateTenantId();
        if ($messages) {
            $errors->setProperty('tenantId', $messages);
            $hasError = true;
        }

        $messages = $this->validateApiBase();
        if ($messages) {
            $errors->setProperty('apiBase', $messages);
            $hasError = true;
        }

        if ($hasError) {
            $error = new Error();
            $error->setProperty('errors', $errors);
            throw new InvalidRequestOptionsException($error);
        }

        return $this;
    }

    public static function newBuilder(): RequestOptionsBuilder
    {
        return new RequestOptionsBuilder();
    }
}
