<?php

namespace Karaden\Param\Message;

use DateTimeInterface;
use Karaden\Exception\InvalidParamsException;
use Karaden\Model\Error;
use Karaden\Model\KaradenObject;

class MessageCreateParams extends MessageParams
{
    public int $serviceId;
    public string $to;
    public string $body;
    public ?array $tags;
    public ?bool $isShorten;
    public ?DateTimeInterface $scheduledAt;
    public ?DateTimeInterface $limitedAt;

    public function __construct(int $serviceId, string $to, string $body, ?array $tags = null, ?bool $isShorten = null, ?DateTimeInterface $scheduledAt = null, ?DateTimeInterface $limitedAt = null)
    {
        $this->serviceId = $serviceId;
        $this->to = $to;
        $this->body = $body;
        $this->tags = $tags;
        $this->isShorten = $isShorten;
        $this->scheduledAt = $scheduledAt;
        $this->limitedAt = $limitedAt;
    }

    public function toPath(): string
    {
        return static::CONTEXT_PATH;
    }

    public function toData(): array
    {
        return [
            'service_id' => $this->serviceId,
            'to' => $this->to,
            'body' => $this->body,
            'tags' => $this->tags ? $this->tags : null,
            'is_shorten' => is_null($this->isShorten) ? null : ($this->isShorten ? 'true' : 'false'),
            'scheduled_at' => $this->scheduledAt ? $this->scheduledAt->format(DATE_ATOM) : null,
            'limited_at' => $this->limitedAt ? $this->limitedAt->format(DATE_ATOM) : null,
        ];
    }

    protected function validateServiceId()
    {
        $messages = [];

        if (! $this->serviceId || $this->serviceId <= 0) {
            $messages[] = 'serviceIdは必須です。';
            $messages[] = '数字を入力してください。'; 
        }

        return $messages;
    }

    protected function validateTo()
    {
        $messages = [];

        if (! $this->to) {
            $messages[] = 'toは必須です。';
            $messages[] = '文字列を入力してください。'; 
        }

        return $messages;
    }

    protected function validateBody()
    {
        $messages = [];

        if (! $this->body) {
            $messages[] = 'bodyは必須です。';
            $messages[] = '文字列を入力してください。'; 
        }

        return $messages;
    }

    public function validate(): MessageParams
    {
        $errors = new KaradenObject();
        $hasError = false;

        $messages = $this->validateServiceId();
        if ($messages) {
            $errors->setProperty('serviceId', $messages);
            $hasError = true;
        }

        $messages = $this->validateTo();
        if ($messages) {
            $errors->setProperty('to', $messages);
            $hasError = true;
        }

        $messages = $this->validateBody();
        if ($messages) {
            $errors->setProperty('body', $messages);
            $hasError = true;
        }

        if ($hasError) {
            $error = new Error();
            $error->setProperty('errors', $errors);
            throw new InvalidParamsException($error);
        }

        return $this;
    }

    public static function newBuilder(): MessageCreateParamsBuilder
    {
        return new MessageCreateParamsBuilder();
    }
}
