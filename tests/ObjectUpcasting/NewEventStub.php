<?php

declare(strict_types=1);


namespace Tests\ObjectUpcasting;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class NewEventStub implements SerializablePayload
{
    public function __construct(public readonly string $foo, public readonly string $bar)
    {}

    public function toPayload(): array
    {
        return ['foo' => $this->foo, 'bar' => $this->bar];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self($payload['foo'], $payload['bar']);
    }
}