<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting\Tests\MessageUpcaster\Doubles;

use Andreo\EventSauce\Upcasting\MessageUpcaster\Event;
use Andreo\EventSauce\Upcasting\MessageUpcaster\MessageUpcaster;
use EventSauce\EventSourcing\Message;

final readonly class EventUpcasterV2Fake implements MessageUpcaster
{
    #[Event(event: DeprecatedEvenFake::class)]
    public function upcast(Message $message): Message
    {
        $event = $message->payload();
        assert($event instanceof DeprecatedEvenFake);

        return new Message(new NewEventFake($event->foo, 'bar'), ['__foo_header' => 'foo']);
    }
}
