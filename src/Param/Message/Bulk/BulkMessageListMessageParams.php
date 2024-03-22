<?php

namespace Karaden\Param\Message\Bulk;

use Karaden\Exception\InvalidParamsException;
use Karaden\Model\Error;
use Karaden\Model\KaradenObject;

class BulkMessageListMessageParams extends BulkMessageParams
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function toPath(): string
    {
        return sprintf("%s/%s/messages", static::CONTEXT_PATH, $this->id);
    }

    protected function validateId()
    {
        $messages = [];

        if ($this->id == '') {
            $messages[] = 'idは必須です。';
            $messages[] = '文字列（UUID）を入力してください。';
        }

        return $messages;
    }

    public function validate(): BulkMessageParams
    {
        $errors = new KaradenObject();
        $hasError = false;

        $messages = $this->validateId();
        if ($messages) {
            $errors->setProperty('id', $messages);
            $hasError = true;
        }

        if ($hasError) {
            $error = new Error();
            $error->setProperty('errors', $errors);
            throw new InvalidParamsException($error);
        }

        return $this;
    }

    public static function newBuilder(): BulkMessageListMessageParamsBuilder
    {
        return new BulkMessageListMessageParamsBuilder();
    }
}
