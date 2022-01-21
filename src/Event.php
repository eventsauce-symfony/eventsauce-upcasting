<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Event
{
    public function __construct(
        public string $event,
    ) {
    }
}
