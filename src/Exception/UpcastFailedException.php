<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting\Exception;

use EventSauce\EventSourcing\EventSauceException;
use RuntimeException;

final class UpcastFailedException extends RuntimeException implements EventSauceException
{
    public static function typeHeaderNotFound(string $header): self
    {
        return new self(
            sprintf(
                'Upcast failed, because type header [%s] not found. Check your message decorator definitions.',
                $header
            )
        );
    }
}
