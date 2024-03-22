<?php

namespace Karaden\Param\Message\Bulk;

use Karaden\Exception\InvalidParamsException;
use Karaden\Model\Error;
use Karaden\Model\KaradenObject;

class BulkMessageCreateParams extends BulkMessageParams
{
    public string $bulkFileId;

    public function __construct(string $bulkFileId)
    {
        $this->bulkFileId = $bulkFileId;
    }

    public function toPath(): string
    {
        return static::CONTEXT_PATH;
    }

    public function toData(): array
    {
        return [
            'bulk_file_id' => $this->bulkFileId,
        ];
    }

    protected function validateBulkFileId()
    {
        $messages = [];

        if ($this->bulkFileId == '') {
            $messages[] = 'bulkFileIdは必須です。';
            $messages[] = '文字列（UUID）を入力してください。';
        }

        return $messages;
    }

    public function validate(): BulkMessageParams
    {
        $errors = new KaradenObject();
        $hasError = false;

        $messages = $this->validateBulkFileId();
        if ($messages) {
            $errors->setProperty('bulkFileId', $messages);
            $hasError = true;
        }

        if ($hasError) {
            $error = new Error();
            $error->setProperty('errors', $errors);
            throw new InvalidParamsException($error);
        }

        return $this;
    }

    public static function newBuilder(): BulkMessageCreateParamsBuilder
    {
        return new BulkMessageCreateParamsBuilder();
    }
}
