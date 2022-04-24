<?php

declare(strict_types=1);

namespace Tests\MessageUpcasting;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class DeprecatedEventStub implements SerializablePayload
{
    public function __construct(public readonly string $foo)
    {
    }

    public function toPayload(): array
    {
        return ['foo' => $this->foo];
    }

    public static function fromPayload(array $payload): static
    {
        return new self($payload['foo']);
    }
}
