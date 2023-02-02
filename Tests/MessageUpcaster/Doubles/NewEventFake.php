<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting\Tests\MessageUpcaster\Doubles;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final readonly class NewEventFake implements SerializablePayload
{
    public function __construct(public string $foo, public string $bar)
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
