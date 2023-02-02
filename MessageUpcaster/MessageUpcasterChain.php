<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting\MessageUpcaster;

use EventSauce\EventSourcing\Message;
use ReflectionObject;

final readonly class MessageUpcasterChain implements MessageUpcaster
{
    /**
     * @var array<MessageUpcaster>
     */
    private array $upcasters;

    public function __construct(MessageUpcaster ...$upcasters)
    {
        $this->upcasters = $upcasters;
    }

    public function upcast(Message $message): Message
    {
        foreach ($this->upcasters as $upcaster) {
            $reflection = new ReflectionObject($upcaster);
            $upcastMethod = $reflection->getMethod('upcast');
            $reflectionAttribute = $upcastMethod->getAttributes(Event::class)[0] ?? null;

            if (null !== $reflectionAttribute) {
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
