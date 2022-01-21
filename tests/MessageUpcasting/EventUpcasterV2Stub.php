<?php

declare(strict_types=1);

namespace Tests\MessageUpcasting;

use Andreo\EventSauce\Upcasting\Event;
use Andreo\EventSauce\Upcasting\MessageUpcaster;
use EventSauce\EventSourcing\Message;

final class EventUpcasterV2Stub implements MessageUpcaster
{
    #[Event(event: DeprecatedEventStub::class)]
    public function upcast(Message $message): Message
    {
        $event = $message->event();
        assert($event instanceof DeprecatedEventStub);

        return new Message(new NewEventStub($event->foo, 'bar'), ['__foo_header' => 'foo']);
    }
}
