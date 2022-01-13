<?php

declare(strict_types=1);

namespace Tests\UpcastingWithEventGuessing;

use Andreo\EventSauce\Upcasting\Exception\UpcastFailedException;
use Andreo\EventSauce\Upcasting\UpcasterChainWithEventGuessing;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Upcasting\Upcaster;
use EventSauce\EventSourcing\Upcasting\UpcastingMessageSerializer;
use PHPUnit\Framework\TestCase;

final class UpcasterChainWithEventGuessingTest extends TestCase
{
    /**
     * @test
     */
    public function upcasting_message(): void
    {
        $serializer = $this->serializer();
        $deprecatedPayload = [
            'headers' => [
                Header::EVENT_TYPE => EventStub::class,
            ],
            'payload' => ['foo' => 'foo'],
        ];
        $newMessage = $serializer->unserializePayload($deprecatedPayload);

        $this->assertInstanceOf(EventStub::class, $newMessage->event());
        $this->assertObjectHasAttribute('bar', $newMessage->event());
        $this->assertEquals('bar', $newMessage->event()->bar);
        $this->assertArrayHasKey('__foo_header', $newMessage->headers());
    }

    /**
     * @test
     */
    public function upcasting_failed_if_type_header_not_found(): void
    {
        $this->expectException(UpcastFailedException::class);
        $serializer = $this->serializer();
        $deprecatedPayload = [
            'headers' => [],
            'payload' => ['foo' => 'foo'],
        ];
        $serializer->unserializePayload($deprecatedPayload);
    }

    private function upcaster(): Upcaster
    {
        return new UpcasterChainWithEventGuessing(
            [new EventUpcasterV2Stub()],
            new DotSeparatedSnakeCaseInflector()
        );
    }

    private function serializer(): UpcastingMessageSerializer
    {
        return new UpcastingMessageSerializer(
            new ConstructingMessageSerializer(),
            $this->upcaster()
        );
    }
}
