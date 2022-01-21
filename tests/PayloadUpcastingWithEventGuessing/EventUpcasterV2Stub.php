<?php

declare(strict_types=1);

namespace Tests\PayloadUpcastingWithEventGuessing;

use Andreo\EventSauce\Upcasting\Event;
use EventSauce\EventSourcing\Upcasting\Upcaster;

final class EventUpcasterV2Stub implements Upcaster
{
    #[Event(event: EventStub::class)]
    public function upcast(array $message): array
    {
        $message['payload']['bar'] = 'bar';
        $message['headers']['__foo_header'] = 'foo';

        return $message;
    }
}
