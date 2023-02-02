<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting\MessageUpcaster;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\MessageSerializer;

final readonly class UpcastingMessageObjectSerializer implements MessageSerializer
{
    private MessageSerializer $messageSerializer;

    private MessageUpcaster $upcaster;

    public function __construct(MessageSerializer $messageSerializer, MessageUpcaster $upcaster)
    {
        $this->messageSerializer = $messageSerializer;
        $this->upcaster = $upcaster;
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeMessage(Message $message): array
    {
        return $this->messageSerializer->serializeMessage($message);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function unserializePayload(array $payload): Message
    {
        $message = $this->messageSerializer->unserializePayload($payload);

        return $this->upcaster->upcast($message);
    }
}
