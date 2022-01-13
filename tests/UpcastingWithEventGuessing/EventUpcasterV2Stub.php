<?php

declare(strict_types=1);

namespace Tests\UpcastingWithEventGuessing;

use Andreo\EventSauce\Upcasting\AsUpcaster;
use EventSauce\EventSourcing\Upcasting\Upcaster;

#[AsUpcaster(
    aggregate: 'test',
    version: 2,
    deprecatedEvent: EventStub::class
)]
final class EventUpcasterV2Stub implements Upcaster
{
    public function upcast(array $message): array
    {
        $message['payload']['bar'] = 'bar';
        $message['headers']['__foo_header'] = 'foo';

        return $message;
    }
}
