<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\MessageSerializer;

final class UpcastingMessageObjectSerializer implements MessageSerializer
{
    private MessageSerializer $eventSerializer;

    private ObjectUpcaster $upcaster;

    public function __construct(MessageSerializer $eventSerializer, ObjectUpcaster $upcaster)
    {
        $this->eventSerializer = $eventSerializer;
        $this->upcaster = $upcaster;
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeMessage(Message $message): array
    {
        return $this->eventSerializer->serializeMessage($message);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function unserializePayload(array $payload): Message
    {
        $message = $this->eventSerializer->unserializePayload($payload);

        return $this->upcaster->upcast($message);
    }
}
