<?php

declare(strict_types=1);

namespace Tests\MessageUpcasting;

use Andreo\EventSauce\Upcasting\MessageUpcaster;
use Andreo\EventSauce\Upcasting\MessageUpcasterChain;
use Andreo\EventSauce\Upcasting\UpcastingMessageObjectSerializer;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use PHPUnit\Framework\TestCase;

final class ObjectUpcasterTest extends TestCase
{
    /**
     * @test
     */
    public function should_upcast_message(): void
    {
        $upcaster = $this->upcaster();
        $deprecatedMessage = new Message(new DeprecatedEventStub('foo'));
        $newMessage = $upcaster->upcast($deprecatedMessage);
        $this->assertInstanceOf(NewEventStub::class, $newMessage->event());
        $this->assertObjectHasAttribute('bar', $newMessage->event());
        $this->assertArrayHasKey('__foo_header', $newMessage->headers());
    }

    /**
     * @test
     */
    public function should_serialize_and_upcast_message(): void
    {
        $serializer = $this->serializer();
        $deprecatedMessage = new Message($deprecatedEvent = new DeprecatedEventStub('foo'));
        $deprecatedMessage = $deprecatedMessage->withHeader(Header::EVENT_TYPE, $deprecatedEvent::class);
        $deprecatedMessagePayload = $serializer->serializeMessage($deprecatedMessage);
        $newMessage = $serializer->unserializePayload($deprecatedMessagePayload);

        $this->assertInstanceOf(NewEventStub::class, $newMessage->event());
        $this->assertObjectHasAttribute('bar', $newMessage->event());
        $this->assertArrayHasKey('__foo_header', $newMessage->headers());
    }

    private function upcaster(): MessageUpcaster
    {
        return new MessageUpcasterChain(new EventUpcasterV2Stub());
    }

    private function serializer(): UpcastingMessageObjectSerializer
    {
        return new UpcastingMessageObjectSerializer(
            new ConstructingMessageSerializer(),
            $this->upcaster()
        );
    }
}
