<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting;

use EventSauce\EventSourcing\Message;
use ReflectionAttribute;
use ReflectionObject;

final class ObjectUpcasterChain implements ObjectUpcaster
{
    /**
     * @param iterable<ObjectUpcaster> $upcasters
     */
    public function __construct(
        private iterable $upcasters
    ){}

    public function upcast(Message $message): Message
    {
        foreach ($this->upcasters as $upcaster) {
            $reflection = new ReflectionObject($upcaster);
            $reflectionAttribute = $reflection->getAttributes(AsUpcaster::class)[0] ?? null;
            assert($reflectionAttribute instanceof ReflectionAttribute);

            $attribute = $reflectionAttribute->newInstance();
            assert($attribute instanceof AsUpcaster);
            if (null !== $deprecatedEvent = $attribute->deprecatedEvent) {
                if ($deprecatedEvent === $message->event()::class) {
                    $message = $upcaster->upcast($message);
                }
            } else {
                $message = $upcaster->upcast($message);
            }
        }

        return $message;
    }
}
