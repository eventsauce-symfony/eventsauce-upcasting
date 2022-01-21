<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting;

use Andreo\EventSauce\Upcasting\Exception\UpcastFailedException;
use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Upcasting\Upcaster;
use ReflectionAttribute;
use ReflectionObject;

final class UpcasterChainWithEventGuessing implements Upcaster
{
    /**
     * @param iterable<Upcaster> $upcasters
     */
    public function __construct(
        private iterable $upcasters,
        private ClassNameInflector $classNameInflector,
        private string $headerEventType = Header::EVENT_TYPE
    ) {
    }

    /**
     * @param array<string, array<mixed>> $message
     *
     * @return array<string, array<mixed>>
     */
    public function upcast(array $message): array
    {
        foreach ($this->upcasters as $upcaster) {
            $reflection = new ReflectionObject($upcaster);
            $upcastMethod = $reflection->getMethod('upcast');
            $reflectionAttribute = $upcastMethod->getAttributes(Event::class)[0] ?? null;

            if ($reflectionAttribute instanceof ReflectionAttribute) {
                $guessEventAttribute = $reflectionAttribute->newInstance();
                assert($guessEventAttribute instanceof Event);

                if ($this->isEventMatch($guessEventAttribute->event, $message)) {
                    $message = $upcaster->upcast($message);
                }
            } else {
                $message = $upcaster->upcast($message);
            }
        }

        return $message;
    }

    /**
     * @param array<string, array<mixed>> $message
     */
    private function isEventMatch(string $deprecatedEvent, array $message): bool
    {
        /** @var string|null $messageEventType */
        $messageEventType = $message['headers'][$this->headerEventType] ?? null;
        if (null === $messageEventType) {
            throw UpcastFailedException::typeHeaderNotFound($this->headerEventType);
        }

        $currentEvent = $this->classNameInflector->typeToClassName($messageEventType);

        return $currentEvent === $deprecatedEvent;
    }
}
