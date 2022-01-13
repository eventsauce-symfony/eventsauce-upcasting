<?php

declare(strict_types=1);

namespace Tests\ObjectUpcasting;

use Andreo\EventSauce\Upcasting\AsUpcaster;
use Andreo\EventSauce\Upcasting\ObjectUpcaster;
use EventSauce\EventSourcing\Message;

#[AsUpcaster(
    aggregate: 'test',
    version: 2,
    deprecatedEvent: DeprecatedEventStub::class
)]
final class EventUpcasterV2Stub implements ObjectUpcaster
{
    public function upcast(Message $message): Message
    {
        $event = $message->event();
        assert($event instanceof DeprecatedEventStub);

        return new Message(new NewEventStub($event->foo, 'bar'), ['__foo_header' => 'foo']);
    }
}
