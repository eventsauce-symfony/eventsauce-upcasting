<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting;

use EventSauce\EventSourcing\Message;
use ReflectionAttribute;
use ReflectionObject;

final class MessageUpcasterChain implements MessageUpcaster
{
    /**
     * @param iterable<MessageUpcaster> $upcasters
     */
    public function __construct(
        private iterable $upcasters
    ) {
    }

    public function upcast(Message $message): Message
    {
        foreach ($this->upcasters as $upcaster) {
            $reflection = new ReflectionObject($upcaster);
            $upcastMethod = $reflection->getMethod('upcast');
            $reflectionAttribute = $upcastMethod->getAttributes(Event::class)[0] ?? null;

            if ($reflectionAttribute instanceof ReflectionAttribute) {
                $guessEventAttribute = $reflectionAttribute->newInstance();
                assert($guessEventAttribute instanceof Event);

                if ($guessEventAttribute->event === $message->event()::class) {
                    $message = $upcaster->upcast($message);
                }
            } else {
                $message = $upcaster->upcast($message);
            }
        }

        return $message;
    }
}
