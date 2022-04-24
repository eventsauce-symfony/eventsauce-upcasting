<?php

declare(strict_types=1);

namespace Tests\MessageUpcasting;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class NewEventStub implements SerializablePayload
{
    public function __construct(public readonly string $foo, public readonly string $bar)
    {
    }

    public function toPayload(): array
    {
        return ['foo' => $this->foo, 'bar' => $this->bar];
    }

    public static function fromPayload(array $payload): static
    {
        return new self($payload['foo'], $payload['bar']);
    }
}
