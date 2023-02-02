<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting\MessageUpcaster;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final readonly class Event
{
    /**
     * @param class-string $event
     */
    public function __construct(
        public string $event,
    ) {
    }
}
