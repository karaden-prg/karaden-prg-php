<?php

namespace Karaden\Param\Message\Bulk;

use Karaden\Exception\InvalidParamsException;
use Karaden\Model\Error;
use Karaden\Model\KaradenObject;

class BulkMessageDownloadParams extends BulkMessageParams
{
    const DEFAULT_MAX_RETRIES = 2;
    const MAX_MAX_RETRIES = 5;
    const MIN_MAX_RETRIES = 1;
    const DEFAULT_RETRY_INTERVAL = 20;
    const MAX_RETRY_INTERVAL = 60;
    const MIN_RETRY_INTERVAL = 10;

    public string $id;
    public string $directoryPath;
    public ?int $maxRetries;
    public ?int $retryInterval;

    public function __construct(string $id, string $directoryPath, int $maxRetries = self::DEFAULT_MAX_RETRIES, int $retryInterval = self::DEFAULT_RETRY_INTERVAL)
    {
        $this->id = $id;
        $this->directoryPath = $directoryPath;
        $this->maxRetries = $maxRetries;
        $this->retryInterval = $retryInterval;
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

    protected function validateDirectoryPath()
    {
        $messages = [];

        if ($this->directoryPath == '') {
            $messages[] = 'directoryPathは必須です。';
            $messages[] = '文字列を入力してください。';
        }
        if (!file_exists($this->directoryPath)) {
            $messages[] = '指定されたディレクトリパスが存在しません。';
        }
        if (!is_dir($this->directoryPath)) {
            $messages[] = '指定されたパスはディレクトリではありません。';
        }
        if (!is_readable($this->directoryPath)) {
            $messages[] = '指定されたディレクトリには読み取り権限がありません。';
        }
        if (!is_writable($this->directoryPath)) {
            $messages[] = '指定されたディレクトリには書き込み権限がありません。';
        }

        return $messages;
    }

    protected function validateMaxRetries()
    {
        $messages = [];

        if ($this->maxRetries < self::MIN_MAX_RETRIES) {
            $messages[] = 'maxRetriesには' . self::MIN_MAX_RETRIES . '以上の整数を入力してください。';
        }
        if ($this->maxRetries > self::MAX_MAX_RETRIES) {
            $messages[] = 'maxRetriesには' . self::MAX_MAX_RETRIES . '以下の整数を入力してください。';
        }

        return $messages;
    }

    protected function validateRetryInterval()
    {
        $messages = [];

        if ($this->retryInterval < self::MIN_RETRY_INTERVAL) {
            $messages[] = 'retryIntervalには' . self::MIN_RETRY_INTERVAL . '以上の整数を入力してください。';
        }
        if ($this->retryInterval > self::MAX_RETRY_INTERVAL) {
            $messages[] = 'retryIntervalには' . self::MAX_RETRY_INTERVAL . '以下の整数を入力してください。';
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

        $messages = $this->validateDirectoryPath();
        if ($messages) {
            $errors->setProperty('directoryPath', $messages);
            $hasError = true;
        }

        $messages = $this->validateMaxRetries();
        if ($messages) {
            $errors->setProperty('maxRetries', $messages);
            $hasError = true;
        }

        $messages = $this->validateRetryInterval();
        if ($messages) {
            $errors->setProperty('retryInterval', $messages);
            $hasError = true;
        }

        if ($hasError) {
            $error = new Error();
            $error->setProperty('errors', $errors);
            throw new InvalidParamsException($error);
        }

        return $this;
    }

    public static function newBuilder(): BulkMessageDownloadParamsBuilder
    {
        return new BulkMessageDownloadParamsBuilder();
    }
}
