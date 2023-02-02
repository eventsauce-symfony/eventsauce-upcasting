<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting\Tests\MessageUpcaster;

use Andreo\EventSauce\Upcasting\MessageUpcaster\MessageUpcaster;
use Andreo\EventSauce\Upcasting\MessageUpcaster\MessageUpcasterChain;
use Andreo\EventSauce\Upcasting\MessageUpcaster\UpcastingMessageObjectSerializer;
use Andreo\EventSauce\Upcasting\Tests\MessageUpcaster\Doubles\DeprecatedEvenFake;
use Andreo\EventSauce\Upcasting\Tests\MessageUpcaster\Doubles\EventUpcasterV2Fake;
use Andreo\EventSauce\Upcasting\Tests\MessageUpcaster\Doubles\NewEventFake;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use PHPUnit\Framework\TestCase;

final class MessageUpcasterTest extends TestCase
{
    /**
     * @test
     */
    public function should_upcast_message(): void
    {
        $upcaster = $this->upcaster();
        $deprecatedMessage = new Message(new DeprecatedEvenFake('foo'));
        $newMessage = $upcaster->upcast($deprecatedMessage);
        $this->assertInstanceOf(NewEventFake::class, $newMessage->event());
        $this->assertObjectHasAttribute('bar', $newMessage->event());
        $this->assertArrayHasKey('__foo_header', $newMessage->headers());
    }

    /**
     * @test
     */
    public function should_serialize_and_upcast_message(): void
    {
        $serializer = $this->serializer();
        $deprecatedMessage = new Message($deprecatedEvent = new DeprecatedEvenFake('foo'));
        $deprecatedMessage = $deprecatedMessage->withHeader(Header::EVENT_TYPE, $deprecatedEvent::class);
        $deprecatedMessagePayload = $serializer->serializeMessage($deprecatedMessage);
        $newMessage = $serializer->unserializePayload($deprecatedMessagePayload);

        $this->assertInstanceOf(NewEventFake::class, $newMessage->event());
        $this->assertObjectHasAttribute('bar', $newMessage->event());
        $this->assertArrayHasKey('__foo_header', $newMessage->headers());
    }

    private function upcaster(): MessageUpcaster
    {
        return new MessageUpcasterChain(new EventUpcasterV2Fake());
    }

    private function serializer(): UpcastingMessageObjectSerializer
    {
        return new UpcastingMessageObjectSerializer(
            new ConstructingMessageSerializer(),
            $this->upcaster()
        );
    }
}
