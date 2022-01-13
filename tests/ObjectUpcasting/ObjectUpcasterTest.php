<?php

declare(strict_types=1);

namespace Tests\ObjectUpcasting;

use Andreo\EventSauce\Upcasting\ObjectUpcaster;
use Andreo\EventSauce\Upcasting\ObjectUpcasterChain;
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
    public function upcasting_message(): void
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
    public function serialize_with_upcasting(): void
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

    private function upcaster(): ObjectUpcaster
    {
        return new ObjectUpcasterChain([new EventUpcasterV2Stub()]);
    }

    private function serializer(): UpcastingMessageObjectSerializer
    {
        return new UpcastingMessageObjectSerializer(
            new ConstructingMessageSerializer(),
            $this->upcaster()
        );
    }
}
